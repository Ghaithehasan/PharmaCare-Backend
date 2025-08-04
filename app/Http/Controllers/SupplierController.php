<?php

namespace App\Http\Controllers;

use App\Events\SupplierRegistered;
use App\Models\Supplier;
use App\Models\SupplierNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{

    public function show_supplier_dashboard()
    {
        $supplier = auth()->user();

        // Calculate order statistics
        $totalOrders = $supplier->orders()->count();
        $pendingOrders = $supplier->orders()->where('status', 'pending')->count();
        $confirmedOrders = $supplier->orders()->where('status', 'confirmed')->count();
        $completedOrders = $supplier->orders()->where('status', 'completed')->count();
        $cancelledOrders = $supplier->orders()->where('status', 'cancelled')->count();

        // Calculate percentages
        $pendingPercentage = $totalOrders > 0 ? round(($pendingOrders / $totalOrders) * 100) : 0;
        $confirmedPercentage = $totalOrders > 0 ? round(($confirmedOrders / $totalOrders) * 100) : 0;
        $completedPercentage = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
        $cancelledPercentage = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100) : 0;

        return view('suppliers.dashboard', compact(
            'supplier',
            'totalOrders',
            'pendingOrders',
            'confirmedOrders',
            'completedOrders',
            'cancelledOrders',
            'pendingPercentage',
            'confirmedPercentage',
            'completedPercentage',
            'cancelledPercentage'
        ));
    }

    public function show_supplier_profile()
    {
        $supplier = auth()->user();
        $order_completed_count = $supplier->orders()->where('status', 'completed')->count();
        return view('suppliers.profile', compact('supplier', 'order_completed_count'));
    }

    /**
     * Display detailed information about a specific supplier.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ShowSupplierDetails($id)
    {
        try {
            $currentSupplier = auth()->user();

            // التحقق من أن المورد يحاول الوصول لبياناته الخاصة فقط
            if ($currentSupplier->id != $id) {
                return response()->json([
                    'status' => false,
                    'status_code' => 403,
                    'message' => 'غير مسموح لك بالوصول لبيانات مورد آخر',
                ], 403);
            }

            $supplier = Supplier::with([
                'orders' => function($query) {
                    $query->with('medicines')
                    ->latest()
                    ->take(5);
                },
                'payments' => function($query) {
                    $query->latest()->take(5);
                }
            ])->find($id);

            if (!$supplier) {
                return response()->json([
                    'status' => false,
                    'status_code' => 404,
                    'message' => __('messages.supplier_not_found'),
                ], 404);
            }

            // حساب إحصائيات المورد
            $supplier_statistics = [
                'orders' => [
                    'total' => $supplier->orders()->count(),
                    'pending' => $supplier->orders()->where('status', 'pending')->count(),
                    'confirmed' => $supplier->orders()->where('status', 'confirmed')->count(),
                    'completed' => $supplier->orders()->where('status', 'completed')->count(),
                    'cancelled' => $supplier->orders()->where('status', 'cancelled')->count(),
                ],

                'payments' => [
                    'total_paid' => $supplier->payments()
                        ->where('payment_status', 'completed')
                        ->sum('amount_paid'),
                    'pending_payments' => $supplier->payments()
                        ->where('payment_status', 'pending')
                        ->sum('amount_paid'),
                ],
                'orders_summary' => [
                    // 'total_amount' => $supplier->orders()->sum('total_amount'),
                    // 'average_order_value' => $supplier->orders()->avg('total_amount'),
                    'last_order_date' => $supplier->orders()->latest()->first()?->order_date,
                    'last_payment_date' => $supplier->payments()
                        ->where('payment_status', 'completed')
                        ->latest()
                        ->first()?->payment_date,
                ],
                'credit_info' => [
                    'credit_limit' => $supplier->credit_limit,
                    'payment_method' => $supplier->payment_method,
                ]
            ];

            // dd();
            // تحضير بيانات الطلبات الأخيرة مع تفاصيلها
            $recentOrders = $supplier->orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'order_date' => $order->order_date,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    // 'items_count' => $order->orderItems->count(),
                    'items' => $order->medicines->map(function($item) {
                        return [
                            'medicine_id' => $item->pivot->medicine_id,
                            'quantity' => $item->pivot->quantity,
                            'unit_price' => $item->pivot->unit_price,
                            'total_price' => $item->pivot->total_price
                        ];
                    })
                ];
            });

            // تحضير بيانات المدفوعات الأخيرة
            $recentPayments = $supplier->payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'payment_date' => $payment->payment_date,
                    'amount_paid' => $payment->amount_paid,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status
                ];
            });

            return response()->json([
                'status' => true,
                'status_code' => 200,
                // 'message' => __('messages.supplier_details_retrieved'),
                'data' => [
                    'supplier' => [
                        'id' => $supplier->id,
                        'company_name' => $supplier->company_name,
                        'contact_person_name' => $supplier->contact_person_name,
                        'phone' => $supplier->phone,
                        'email' => $supplier->email,
                        'address' => $supplier->address,
                        'bio' => $supplier->bio,
                        'is_active' => $supplier->is_active,
                        'created_at' => $supplier->created_at,
                        'updated_at' => $supplier->updated_at
                    ],
                    'statistics' => $supplier_statistics,
                    'recent_orders' => $recentOrders,
                    'recent_payments' => $recentPayments
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                // 'message' => __('messages.error_retrieving_supplier_details'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:50|unique:suppliers,company_name',
            'contact_person_name' => 'nullable|string|max:50',
            'phone' => 'required|numeric|digits_between:10,10|unique:suppliers,phone',
            'email' => 'nullable|email|unique:suppliers,email',
            'address' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,bank_transfer,credit',
            'credit_limit' => 'nullable|numeric|min:0|max:100000',
            'is_active' => 'boolean',
        ]);

        try {
            // ✅ 2️⃣ إنشاء المورد الجديد (Create Supplier)
            $supplier = Supplier::create([
                'company_name' => $validatedData['company_name'],
                'contact_person_name' => $validatedData['contact_person_name'] ?? null,
                'phone' => $validatedData['phone'],
                'email' => $validatedData['email'] ?? null,
                'password' => Hash::make('password'),
                'address' => $validatedData['address'] ?? null,
                'payment_method' => $validatedData['payment_method']?? 'cash',
                'credit_limit' => $validatedData['credit_limit']?? 0,
                'is_active' => $validatedData['is_active'] ?? true,
            ]);

            event(new SupplierRegistered($supplier));

            return response()->json([
                'supplier' => $supplier,
                'status_code' => 201,
                'message' => __('messages.supplier_created'),
            ], 201);

        } catch (\Exception $e) {
            // ❌ 3️⃣ التقاط الأخطاء (Exception Handling)
            return response()->json([
                'status' => 'error',
                'message' => __('messages.supplier_creation_failed'),
                'error_details' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }


    public function show_account()
    {
        $supplier = auth()->user();
        return view('suppliers.account_setting' , compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function delete_account()
    {
        $supplier = auth()->user();
        $supplier->forceDelete();
        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd();
        $supplier = auth()->user();
        $request->validate([
        'contact_person_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
        'password' => 'nullable|min:6',
        'phone' => 'nullable|string',
        ]);

        // تحديث بيانات المورد
        $supplier->contact_person_name = $request->contact_person_name??$supplier->contact_person_name;
        $supplier->email = $request->email??$supplier->email;
        if ($request->filled('password')) {
            $supplier->password = Hash::make($request->password);
        }
        $supplier->phone = $request->phone??$supplier->phone;
        $supplier->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات المورد بنجاح!');
    }

    public function dis_active_supplier($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            // التحقق من أن المورد نشط حالياً
            if (!$supplier->is_active) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'المورد معطل بالفعل'
                ], 400);
            }

            // تعطيل المورد
            $supplier->is_active = false;
            $supplier->save();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'تم تعطيل المورد بنجاح',
                'data' => [
                    'id' => $supplier->id,
                    'company_name' => $supplier->company_name,
                    'is_active' => $supplier->is_active
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء تعطيل المورد',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function index()
    {
        $suppliers = Supplier::with(['orders.invoice.payments'])->select([
            'id',
            'company_name',
            'contact_person_name',
            'phone',
            'email',
            'is_active'
        ])->get();

        // حساب المشتريات غير المدفوعة لكل مورد
        $suppliersWithUnpaidAmounts = $suppliers->map(function($supplier) {
            $totalUnpaidAmount = 0;

            foreach($supplier->orders as $order) {
                if($order->invoice) {
                    $invoice = $order->invoice;
                    $totalPaid = $invoice->payments->where('status', 'confirmed')->sum('paid_amount');
                    $unpaidAmount = $invoice->total_amount - $totalPaid;

                    if($unpaidAmount > 0) {
                        $totalUnpaidAmount += $unpaidAmount;
                    }
                }
            }

            return [
                'id' => $supplier->id,
                'company_name' => $supplier->company_name,
                'contact_person_name' => $supplier->contact_person_name,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'is_active' => $supplier->is_active,
                'unpaid_purchases' => $totalUnpaidAmount
            ];
        });

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الموردين بنجاح',
            'data' => $suppliersWithUnpaidAmounts,
            'count' => $suppliers->count()
        ]);
    }


    public function show($id)
    {
        $supplier = Supplier::with(['orders.orderItems'])->find($id);

        if (!$supplier) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'المورد غير موجود',
            ], 404);
        }

        // حساب الإحصائيات بكفاءة
        $orders = $supplier->orders;
        $totalOrders = $orders->count();
        $totalPurchases = $orders->sum(function($order) {
            return $order->orderItems->sum('total_price');
        });

        // إحصائيات الطلبات حسب الحالة
        $orderStats = [
            'pending' => $orders->where('status', 'pending')->count(),
            'confirmed' => $orders->where('status', 'confirmed')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب بيانات المورد بنجاح',
            'data' => [
                'supplier' => [
                    'id' => $supplier->id,
                    'contact_person_name' => $supplier->contact_person_name,
                    'company_name' => $supplier->company_name,
                    'phone' => $supplier->phone,
                    'email' => $supplier->email,
                    'address' => $supplier->address,
                ],
                'statistics' => [
                    'total_orders' => $totalOrders,
                    'total_purchases' => $totalPurchases,
                    'order_status_breakdown' => $orderStats,
                    'average_order_value' => $totalOrders > 0 ? round($totalPurchases / $totalOrders, 2) : 0,
                ]
            ]
        ]);
    }


    public function destroy(Supplier $supplier)
    {
        try {
            // التحقق من وجود طلبات نشطة للمورد
            $activeOrders = $supplier->orders()->whereIn('status', ['pending', 'processing'])->exists();
            if ($activeOrders) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.cannot_delete_supplier_with_active_orders'),
                    'data' => null
                ], 422);
            }
            // بدء المعاملة
            DB::beginTransaction();

            try {
                // حذف جميع العلاقات المرتبطة
                $supplier->orders()->delete();
                $supplier->payments()->delete();
                $supplier->notifications()->delete();

                // حذف المورد
                $supplier->delete();

                DB::commit();

                return response()->json([
                    // 'status' => true,
                    // 'message' => __('messages.supplier_deleted_successfully'),
                    'data' => null
                ], 200);

            } catch (\Exception $e) {
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('messages.error_deleting_supplier'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add method to mark notification as read

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:suppliers,email',
        'password' => 'required|min:6|max:8'
    ]);

    $supplier = Supplier::where('email', $request->email)->first();
    if ($supplier && Hash::check($request->password , $supplier->password)) {
        Auth::login($supplier); // تسجيل المورد باستخدام Auth
        return redirect()->route('dashboard_page');
    }

    return back()->withErrors(['email' => 'بيانات تسجيل الدخول غير صحيحة!']);
}

public function logout()
{
    Auth::logout(); // تسجيل الخروج بشكل آمن
    return redirect()->route('home')->with('message', 'تم تسجيل الخروج بنجاح!');
}

    public function showSupplierPurchases($id)
    {
        try {
            $supplier = Supplier::with([
                'orders.invoice.payments',
                'orders.orderItems.medicine'
            ])->findOrFail($id);

            // تحضير بيانات المشتريات
            $purchases = $supplier->orders->where('status','completed')->map(function($order) {
                $invoice = $order->invoice;
                $totalPaid = 0;
                $paymentStatus = 'غير مدفوع';

                if ($invoice) {
                    $totalPaid = $invoice->payments->where('status', 'confirmed')->sum('paid_amount');
                    $unpaidAmount = $invoice->total_amount - $totalPaid;

                    if ($unpaidAmount <= 0) {
                        $paymentStatus = 'مدفوع بالكامل';
                    } elseif ($totalPaid > 0) {
                        $paymentStatus = 'مدفوع جزئياً';
                    }
                }

                return [

                    'purchase' => $invoice ? [

                        'purchase_due_date' => $invoice->due_date,
                        'total_amount' => $invoice->total_amount,
                        'total_paid' => $totalPaid,
                        'unpaid_amount' => $invoice->total_amount - $totalPaid,
                        'payment_status' => $paymentStatus,
                    ] : null,
                    'items' => $order->orderItems->map(function($item) {
                        return [
                            'medicine_name' => $item->medicine->medicine_name,
                            'quantity' => $item->quantity,

                        ];
                    }),
                    'items_count' => $order->orderItems->count(),
                ];
            });

            // حساب الإحصائيات
            $totalOrders = $purchases->count();
            $totalInvoiced = $purchases->sum(function($purchase) {
                return $purchase['purchase']['total_amount'] ?? 0;
            });
            $totalPaid = $purchases->sum(function($purchase) {
                return $purchase['purchase']['total_paid'] ?? 0;
            });
            $totalUnpaid = $totalInvoiced - $totalPaid;

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'تم جلب مشتريات المورد بنجاح',
                'data' => [
                    'supplier' => [
                        'id' => $supplier->id,
                        'company_name' => $supplier->company_name,
                        'contact_person_name' => $supplier->contact_person_name,
                    ],
                    'purchases' => $purchases,
                    'statistics' => [
                        'total_orders' => $totalOrders,
                        'total_purchases' => $totalInvoiced,
                        'total_paid' => $totalPaid,
                        'total_unpaid' => $totalUnpaid,
                        'payment_percentage' => $totalInvoiced > 0 ? round(($totalPaid / $totalInvoiced) * 100, 2) : 0,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء جلب مشتريات المورد',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
