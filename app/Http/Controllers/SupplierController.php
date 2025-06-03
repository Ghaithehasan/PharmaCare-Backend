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
        $supplier= auth()->user();
        return view('suppliers.dashboard' , compact('supplier'));
    }

    public function show_supplier_profile()
    {
        $supplier = auth()->user();
        return view('suppliers.profile' , compact('supplier'));
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

            // return response()->json([
            //     'status' => true,
            //     'status_code' => 200,
            //     'supplier' => $supplier,
            // ], 200);

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
        $supplier = auth()->user();
        $request->validate([
        'contact_person_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
        'password' => 'required|min:6',
        'bio' => 'nullable|string|max:500',
        ]);

        // تحديث بيانات المورد
        $supplier->contact_person_name = $request->contact_person_name??$supplier->contact_person_name;
        $supplier->email = $request->email??$supplier->email;
        if ($request->filled('password')) {
            $supplier->password = Hash::make($request->password);
        }
        $supplier->bio = $request->bio??$supplier->bio;
        $supplier->save();
        return redirect()->back()->with('success', 'تم تحديث بيانات المورد بنجاح!');
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
}
