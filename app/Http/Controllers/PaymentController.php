<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'paid_amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bank_transfer',
            'payment_date'=> 'required|date',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'status'=> 'required|in:pending,confirmed,rejected',
            'notes'=>'nullable|string|max:500'
        ]);

        $filePath = null;
        if($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $fileName = uniqid('proof_') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('payments-proofs', $fileName, 'public');
        }

        $payment = Payment::create([
            'invoice_id' => $validated['invoice_id'],
            'paid_amount' => $validated['paid_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_proof' => $filePath,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if($payment) {
            return response()->json([
                'status' => true,
                'message' => 'تم إضافة الدفع بنجاح وجاري مراجعتها.',
                'payment' => $payment,
                'payment_id' => $payment->id,
                'payment_proof_url' => $filePath ? asset('storage/' . $filePath) : null,
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة المدفوعة.'
            ], 500);
        }
    }

    public function show_all_payments()
    {
        $supplier = auth()->user();
    
        $payments = \App\Models\Payment::whereHas('invoice', function($query) use ($supplier) {
            $query->whereHas('order', function($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            });
        })
        ->with(['invoice.order','invoice.order.supplier']) // جلب بيانات الفاتورة والطلب المرتبط
        ->orderBy('payment_date', 'desc')
        ->get();
        return view('payments.show_all_payments', compact('payments','supplier'));
    }

    public function show_all_pending_payments()
    {
        $supplier = auth()->user();
        $payments_pending = \App\Models\Payment::whereHas('invoice',function($query) use($supplier){
            $query->whereHas('order',function($q) use ($supplier){
                $q->where('supplier_id',$supplier->id);
            });
        })
        ->with(['invoice.order','invoice.order.supplier'])
        ->where('status','pending')
        ->orderBy('payment_date', 'desc')
        ->paginate(10); // إضافة ترقيم الصفحات

        return view('payments.show_pending_payments',compact('payments_pending','supplier'));
    }


    public function show_all_rejected_payments()
    {
        $supplier = auth()->user();
        $payments_rejected = \App\Models\Payment::whereHas('invoice',function($query) use($supplier){
            $query->whereHas('order',function($q) use ($supplier){
                $q->where('supplier_id',$supplier->id);
            });
        })
        ->with(['invoice.order','invoice.order.supplier'])
        ->where('status','rejected')
        ->orderBy('payment_date', 'desc')
        ->paginate(10); // إضافة ترقيم الصفحات

        return view('payments.show_rejected_payments',compact('payments_rejected','supplier'));
    }


    public function show_all_confirmed_payments()
    {
        
        $supplier = auth()->user();
        $payments_confirmed = \App\Models\Payment::whereHas('invoice',function($query) use($supplier){
            $query->whereHas('order',function($q) use ($supplier){
                $q->where('supplier_id',$supplier->id);
            });
        })
        ->with(['invoice.order','invoice.order.supplier'])
        ->where('status','confirmed')
        ->orderBy('payment_date', 'desc')
        ->paginate(10); // إضافة ترقيم الصفحات

        return view('payments.show_accepted_payments',compact('payments_confirmed','supplier'));

    }


    public function change_payment_status(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string|max:500',
            'rejection_reason' => 'required_if:status,rejected|string|max:500'
        ]);

        try {
            $payment = Payment::findOrFail($id);
            
            $supplier = auth()->user();
            
            // التحقق من أن المدفوعة معلقة
            if ($payment->status !== 'pending') {
                return redirect()->back()->with('error', 'لا يمكن تعديل حالة مدفوعة غير معلقة');
            }

            $payment->status = $request->status;
            
            if ($request->status === 'confirmed') {
                $payment->notes = $request->notes;
                $message = 'تم قبول المدفوعة بنجاح';
                $payment->save();
                // تحديث حالة الفاتورة بناءً على إجمالي المدفوعات
                $this->updateInvoiceStatus($payment->invoice);
                
            } else {
                $payment->notes = $request->rejection_reason;
                $message = 'تم رفض المدفوعة بنجاح';
            }


            return redirect()->back()->with('payment_updated', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة المدفوعة');
        }
    }

    /**
     * تحديث حالة الفاتورة بناءً على إجمالي المدفوعات المقدمة
     */
    private function updateInvoiceStatus($invoice)
    {
        // حساب إجمالي المدفوعات المقبولة للفاتورة
        $totalPaid = \App\Models\Payment::where('invoice_id', $invoice->id)
            ->where('status', 'confirmed')
            ->sum('paid_amount');


        // تحديث حالة الفاتورة
        if ($totalPaid >= $invoice->total_amount) {
            // إذا كان إجمالي المدفوعات يساوي أو أكبر من إجمالي الفاتورة
            $invoice->status = 'paid';
            $invoice->due_date = now();
        } elseif ($totalPaid > 0) {
            // إذا كان هناك مدفوعات جزئية
            $invoice->status = 'partially';
        } else {
            // إذا لم توجد مدفوعات
            $invoice->status = 'unpaid';
        }

        $invoice->save();
    }

}

