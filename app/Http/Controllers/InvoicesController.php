<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoices;


class InvoicesController extends Controller
{
    public function index()
    {
        $supplier = auth()->user();
        $invoices = Invoices::with(['order', 'order.supplier'])
            ->whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })
            ->where('is_archived',false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('invoices.show_all_invoices', compact('invoices', 'supplier'));
    }

    public function show_all_invoice_with_filter(Request $request)
    {
        // فلترة متقدمة للفواتير
        $query = Invoices::with(['order', 'order.supplier', 'order.orderItems.medicine'])->where('is_archived',false);

        // فلترة حسب المورد
        if ($request->filled('supplier_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب رقم الفاتورة
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        // فلترة حسب التاريخ (من - إلى)
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // ترتيب حسب الأحدث
        $query->orderBy('created_at', 'desc');

        // جلب النتائج مع ترقيم
        $invoices = $query->paginate(20);

        // تبسيط البيانات
        $data = $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'total_amount' => $invoice->total_amount,
                'order_id' => $invoice->order ? $invoice->order->id : null,
                'order_number' => $invoice->order ? $invoice->order->order_number : null,
                'supplier_name' => $invoice->order && $invoice->order->supplier ? $invoice->order->supplier->contact_person_name : null,
                'items_count' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->count() : 0,
                'medicines' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine ? $item->medicine->medicine_name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $invoices->total(),
            'message' => 'تم جلب جميع الفواتير بنجاح',
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'invoices' => $data,
        ]);
    }
    public function show_paid_invoices_api(Request $request)
    {
        $query = Invoices::with(['order', 'order.supplier', 'order.orderItems.medicine'])
            ->where('status', 'paid')
            ->where('is_archived',false);

        // يمكن إضافة فلترة إضافية هنا إذا رغبت
        if ($request->filled('supplier_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');
        $invoices = $query->paginate(20);

        $data = $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'total_amount' => $invoice->total_amount,
                'order_id' => $invoice->order ? $invoice->order->id : null,
                'order_number' => $invoice->order ? $invoice->order->order_number : null,
                'supplier_name' => $invoice->order && $invoice->order->supplier ? $invoice->order->supplier->contact_person_name : null,
                'items_count' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->count() : 0,
                'medicines' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine ? $item->medicine->medicine_name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $invoices->total(),
            'message' => 'تم جلب الفواتير المدفوعة بنجاح',
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'invoices' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|in:unpaid,paid,cancelled',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric',
        ]);

        $invoice = \App\Models\invoices::create($validated);

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice
        ], 201);
    }

    public function download($id)
    {
        try {
            $invoice = \App\Models\invoices::with(['order.orderItems.medicine', 'order.supplier'])
                ->findOrFail($id);
            
            $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            
            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تحميل الفاتورة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show_pdf_invoice($id)
    {
        try {
            $invoice = \App\Models\invoices::with(['order.orderItems.medicine', 'order.supplier'])
                ->findOrFail($id);
            $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            abort(404, 'Invoice not found');
        }
    }

    public function show_un_paid_invoices()
    {
        $supplier = auth()->user();
        $invoices = Invoices::with(['order','order.supplier'])
        ->whereHas('order',function($q) use ($supplier){
            $q->where('supplier_id',$supplier->id);
        })->where('status','unpaid')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('invoices.show_unpaid_invoices',compact('invoices','supplier'));
    }

    public function show_paid_invoices()
    {
        $supplier = auth()->user();
        $invoices = Invoices::with(['order','order.supplier'])
            ->whereHas('order',function($q) use ($supplier){
                $q->where('supplier_id',$supplier->id);
            })
            ->where('status','paid')
            ->where('is_archived',false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('invoices.show_paid_invoices',compact('invoices','supplier'));
    }

    public function partially()
    {
        $supplier = auth()->user();
        $invoices = Invoices::with(['order','order.supplier'])
            ->whereHas('order',function($q) use ($supplier){
                $q->where('supplier_id',$supplier->id);
            })
            ->where('status','partially')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('invoices.show_partially_invoices',compact('invoices','supplier'));
    }


    public function add_to_archive(Request $request, $id)
    {
        try {
            $invoice = Invoices::findOrFail($id);            
            // أرشفة الفاتورة
            $invoice->is_archived = true;
            $invoice->save();
            
            // إضافة رسالة نجاح
            return redirect()->back()->with('add_archive', 'تم أرشفة بنجاح');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء أرشفة الفاتورة: ' . $e->getMessage());
        }
    }


    public function show_archive_invoice(Request $request)
    {
        $supplier = auth()->user();
        $selectedYear = $request->get('year', date('Y'));
        
        // جلب الفواتير المؤرشفة مع العلاقات
        $invoices = Invoices::with(['order', 'order.supplier', 'order.orderItems.medicine'])
            ->whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })
            ->where('is_archived', true)
            ->when($selectedYear, function($query) use ($selectedYear) {
                return $query->whereYear('created_at', $selectedYear);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // جلب السنوات المتاحة للأرشفة
        $availableYears = Invoices::whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })
            ->where('is_archived', true)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // إحصائيات الأرشفة
        $archiveStats = [
            'total_archived' => Invoices::whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })->where('is_archived', true)->count(),
            'this_year' => Invoices::whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })->where('is_archived', true)->whereYear('created_at', date('Y'))->count(),
            'total_amount' => Invoices::whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })->where('is_archived', true)->sum('total_amount'),
        ];

        return view('invoices.archive_invoice', compact('invoices', 'supplier', 'selectedYear', 'availableYears', 'archiveStats'));
    }

    public function show_partially_paid_invoices_api(Request $request)
    {
        $query = Invoices::with(['order', 'order.supplier', 'order.orderItems.medicine'])
            ->where('status', 'partially')
            ->where('is_archived',false);

        if ($request->filled('supplier_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');
        $invoices = $query->paginate(20);

        $data = $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'total_amount' => $invoice->total_amount,
                'order_id' => $invoice->order ? $invoice->order->id : null,
                'order_number' => $invoice->order ? $invoice->order->order_number : null,
                'supplier_name' => $invoice->order && $invoice->order->supplier ? $invoice->order->supplier->contact_person_name : null,
                'items_count' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->count() : 0,
                'medicines' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine ? $item->medicine->medicine_name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $invoices->total(),
            'message' => 'تم جلب الفواتير المدفوعة جزئياً بنجاح',
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'invoices' => $data,
        ]);
    }

    public function show_unpaid_invoices_api(Request $request)
    {
        $query = Invoices::with(['order', 'order.supplier', 'order.orderItems.medicine'])
            ->where('status', 'unpaid')
            ->where('is_archived',false);

        if ($request->filled('supplier_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $query->orderBy('created_at', 'desc');
        $invoices = $query->paginate(20);

        $data = $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'total_amount' => $invoice->total_amount,
                'order_id' => $invoice->order ? $invoice->order->id : null,
                'order_number' => $invoice->order ? $invoice->order->order_number : null,
                'supplier_name' => $invoice->order && $invoice->order->supplier ? $invoice->order->supplier->contact_person_name : null,
                'items_count' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->count() : 0,
                'medicines' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine ? $item->medicine->medicine_name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }) : [],
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $invoices->total(),
            'message' => 'تم جلب الفواتير الغير مدفوعة بنجاح',
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'invoices' => $data,
        ]);
    }

    public function download_invoice_pdf_api($id)
    {
        try {
            $invoice = Invoices::with(['order.orderItems.medicine', 'order.supplier'])->findOrFail($id);
            $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            $fileName = 'invoice-' . $invoice->invoice_number . '.pdf';
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تجهيز الفاتورة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function view_invoice_pdf_api($id)
    {
        try {
            $invoice = Invoices::with(['order.orderItems.medicine', 'order.supplier'])->findOrFail($id);
            $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            $fileName = 'invoice-' . $invoice->invoice_number . '.pdf';
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="'.$fileName.'"');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء تجهيز الفاتورة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: عرض تفاصيل فاتورة مع جميع المدفوعات المرتبطة بها
     */
    public function show_invoice_with_payments_api($id)
    {
        try {
            $invoice = Invoices::with([
                'order',
                'order.supplier',
                'order.orderItems.medicine',
                'payments' // جلب المدفوعات المرتبطة
            ])->findOrFail($id);

            $invoiceData = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'total_amount' => $invoice->total_amount,
                'notes' => $invoice->notes,
                'order_id' => $invoice->order ? $invoice->order->id : null,
                'order_number' => $invoice->order ? $invoice->order->order_number : null,
                'supplier_name' => $invoice->order && $invoice->order->supplier ? $invoice->order->supplier->contact_person_name : null,
                'items_count' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->count() : 0,
                'medicines' => $invoice->order && $invoice->order->orderItems ? $invoice->order->orderItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine ? $item->medicine->medicine_name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->total_price,
                    ];
                }) : [],
            ];

            $payments = $invoice->payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'paid_amount' => $payment->paid_amount,
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->payment_date,
                    'payment_proof_url' => $payment->payment_proof ? asset('storage/' . $payment->payment_proof) : null,
                    'status' => $payment->status,
                    'notes' => $payment->notes,
                    'created_at' => $payment->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'تم جلب تفاصيل الفاتورة والمدفوعات بنجاح',
                'invoice' => $invoiceData,
                'payments' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الفاتورة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
