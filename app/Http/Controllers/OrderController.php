<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SupplierPayment;
use App\Models\Medicine;
use App\Events\OrderEmailEvent;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.expiry_date' => 'required|date|after:today',
            'payment' => 'required|array',
            'payment.payment_date' => 'required|date',
            'payment.payment_method' => 'required|in:cash,bank_transfer,credit',
            'payment.amount_paid' => 'nullable|numeric|min:0'
        ]);
        // dd($request->items[0]);

        try {
            DB::beginTransaction();

            // إنشاء رقم الطلبية
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            // إنشاء الطلبية
            $order = Order::create([
                'supplier_id' => $validated['supplier_id'],
                'order_number' => $orderNumber,
                'order_date' => $validated['order_date'],
                'status' => 'pending'
            ]);

            $totalAmount = 0;
            $orderItems = [];

            // إضافة عناصر الطلبية
            foreach ($validated['items'] as $item) {
                $medicine = Medicine::findOrFail($item['medicine_id']);
                // استخدام السعر المقدم من الصيدلاني أو سعر المورد كقيمة افتراضية
                $unitPrice = $item['unit_price'] ?? $medicine->supplier_price;
                $totalPrice = $item['quantity'] * $unitPrice;
                $totalAmount += $totalPrice;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'expiry_date' => $item['expiry_date'],
                    'last_notification_date' => null
                ]);

                $orderItems[] = [
                    'medicine_id' => $medicine->id,
                    'medicine_name' => $medicine->medicine_name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'expiry_date' => $item['expiry_date'],
                    'default_supplier_price' => $medicine->supplier_price
                ];
            }

            // إنشاء سجل الدفع
            SupplierPayment::create([
                'supplier_id' => $validated['supplier_id'],
                'payment_date' => $validated['payment']['payment_date'],
                'payment_method' => $validated['payment']['payment_method'],
                'payment_status' => 'pending',
                'amount_paid' => $validated['payment']['amount_paid'] ?? $totalAmount
            ]);

            DB::commit();

            // إرسال الإيميل مع تفاصيل الطلبية
            event(new OrderEmailEvent($order, $orderItems));

            return response()->json([
                'status' => true,
                'status_code' => 201,
                'message' => 'تم إنشاء الطلبية بنجاح',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'order_date' => $order->order_date,
                        'status' => $order->status,
                        'items' => $orderItems,
                        'total_amount' => $totalAmount
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء إنشاء الطلبية',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Order::with(['supplier', 'orderItems'])
            ->select('orders.*');

        // فلتر حسب الحالة
        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        // فلتر حسب المورد
        if ($request->query('supplier_id')) {
            $query->where('supplier_id', $request->query('supplier_id'));
        }

        // فلتر حسب نطاق التاريخ
        if ($request->query('date_from')) {
            $query->whereDate('order_date', '>=', $request->query('date_from'));
        }

        if ($request->query('date_to')) {
            $query->whereDate('order_date', '<=', $request->query('date_to'));
        }

        // البحث في رقم الطلبية أو اسم المورد
        if ($request->query('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->query('search') . '%')
                    ->orWhereHas('supplier', function ($q) use ($request) {
                        $q->where('contact_person_name', 'like', '%' . $request->query('search') . '%');
                    });
            });
        }

        $orders = $query->latest()->paginate(10);

        $orders->getCollection()->transform(function ($order) {
            $totalAmount = $order->orderItems->sum('total_price');
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'order_date' => $order->order_date,
                'status' => $order->status,
                'supplier' => [
                    'id' => $order->supplier->id,
                    'name' => $order->supplier->contact_person_name
                ],
                'total_amount' => $totalAmount,
                'items_count' => $order->orderItems->count(),
                'created_at' => $order->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الطلبات بنجاح',
            'data' => [
                'orders' => $orders,
                'filters' => [
                    'statuses' => ['pending', 'confirmed', 'completed', 'cancelled'],
                    'date_from' => $request->query('date_from'),
                    'date_to' => $request->query('date_to'),
                    'supplier_id' => $request->query('supplier_id'),
                    'search' => $request->query('search')
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['supplier', 'orderItems.medicine'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'الطلبية غير موجودة',
                'errors' => [
                    'order_id' => ['لم يتم العثور على طلبية بهذا المعرف']
                ]
            ], 404);
        }

        $orderDetails = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'order_date' => $order->order_date,
            'status' => $order->status,
            'supplier' => [
                'id' => $order->supplier->id,
                'name' => $order->supplier->contact_person_name,
                'phone' => $order->supplier->phone,
                'email' => $order->supplier->email
            ],
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'medicine' => [
                        'id' => $item->medicine->id,
                        'name' => $item->medicine->medicine_name,
                        'code' => $item->medicine->bar_code
                    ],
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'expiry_date' => $item->expiry_date
                ];
            }),
            'total_amount' => $order->orderItems->sum('total_price'),
            'items_count' => $order->orderItems->count(),
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $order->updated_at->format('Y-m-d H:i:s')
        ];

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب تفاصيل الطلبية بنجاح',
            'data' => $orderDetails
        ]);
    }

    public function destroy($id)
    {


        try {
            DB::beginTransaction();

            $order = Order::with(['orderItems'])->find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'status_code' => 404,
                    'message' => 'الطلبية غير موجودة',
                    'errors' => [
                        'order_id' => ['لم يتم العثور على طلبية بهذا المعرف']
                    ]
                ], 404);
            }

            // التحقق من حالة الطلبية
            if ($order->status === 'completed') {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'لا يمكن حذف طلبية مكتملة',
                    'errors' => [
                        'status' => ['الطلبية مكتملة ولا يمكن حذفها']
                    ]
                ], 400);
            }

            // حذف المدفوعات المرتبطة بالطلبية
            SupplierPayment::where('supplier_id', $order->supplier_id)
                ->delete();

 

            // حذف الطلبية
            $order->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'تم حذف الطلبية وجميع البيانات المرتبطة بها بنجاح',
                'data' => [
                    'order_id' => $id,
                    'deleted_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء حذف الطلبية',
                'errors' => [
                    'system' => ['حدث خطأ غير متوقع، يرجى المحاولة مرة أخرى']
                ]
            ], 500);
        }
    }
}
