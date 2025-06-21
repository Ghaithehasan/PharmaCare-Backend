<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use App\Mail\OrderAcceptedMail;
use Illuminate\Support\Facades\Mail;
use App\Events\ConifermOrder;
use App\Events\CancelledOrder;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['orderItems.medicine', 'supplier'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        $supplier = auth()->user();    
        return view('orders.show_new_orders', compact('orders','supplier'));
    }

    public function accepted()
    {
        $orders = Order::with(['orderItems.medicine', 'supplier'])
            ->where('status', 'confirmed')
            ->latest()
            ->get();
        $supplier = auth()->user();    
        return view('orders.show_accepted_orders', compact('orders','supplier'));
    }


 
    /**
     * Show the form for creating a new resource.
     */
    public function show_All_orders()
    {
        $orders = Order::with(['orderItems.medicine', 'supplier'])
            ->latest()
            ->get();
        $supplier = auth()->user();    
        return view('orders.show_all_orders', compact('orders','supplier'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function cancelled(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $CancelReason = $request->cancellation_reason;

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Update order status
            $order->update([
                'status' => 'cancelled',
                'note' => $request->cancellation_reason
            ]);
            
            
            // إطلاق الحدث لإرسال الإيميل للصيدلاني
            $pharmacyEmail = 'matrex663@gmail.com'; // يمكن استبداله لاحقاً بإيميل الصيدلية من قاعدة البيانات
            event(new CancelledOrder($order, $order->supplier, $pharmacyEmail, $CancelReason));

            return back()->with('cancel_order','تم الغاء الطلبية');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء إلغاء الطلبية'
            ], 500);
        }
    }


    public function ShowPageComplete($id)
    {
        try {
            $order = Order::with(['supplier', 'orderItems.medicine'])->findOrFail($id);
            $supplier = $order->supplier;
            
            // التحقق من أن الطلبية في حالة "تم الشحن" أو "مقبول"
            if ($order->status !== 'confirmed') {
                return redirect()->back()->with('error', 'لا يمكن تأكيد استلام هذه الطلبية في حالتها الحالية');
            }
            
            return view('orders.Accept_the_order', compact('order','supplier'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحميل صفحة تأكيد الطلبية');
        }
    }

    /**
     * تأكيد استلام الطلبية وإكمالها
     */
    public function completeOrder(Request $request, $id)
    {
        try {
            $order = Order::with(['supplier', 'orderItems.medicine'])->findOrFail($id);
            
            // التحقق من أن الطلبية في حالة "تم الشحن" أو "مقبول"
            if ($order->status !== 'confirmed' ) {
                return redirect()->back()->with('error', 'لا يمكن تأكيد استلام هذه الطلبية في حالتها الحالية');
            }
            
            // تحديث حالة الطلبية إلى "مكتمل"
            $order->update([
                'status' => 'completed',
                'note' => $order->note ? $order->note . "\nتم تأكيد الاستلام في: " . now()->format('Y-m-d H:i:s') : "تم تأكيد الاستلام في: " . now()->format('Y-m-d H:i:s')
            ]);
            
            // عرض صفحة تهنئة للصيدلاني
            return view('orders.order_completed', compact('order'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تأكيد استلام الطلبية');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function PrintOrder($id)
    {
        $order = Order::with(['orderItems.medicine', 'supplier'])->findOrFail($id);
        $supplier = $order->supplier;
        return view('orders.print_order', compact('order', 'supplier'));
    }


    public function ExportOrder()
    {
        return Excel::download(new OrderExport(), 'orders.xlsx');
        // return Excel::download(new OrderExport('completed'), 'completed_orders.xlsx');


        // For all orders
        // return Excel::download(new OrderExport(null), 'all_orders.xlsx');
    }


    public function AcceptOrder(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'expected_delivery_date' => 'required|date|after:today',
                'delivery_notes' => 'nullable|string|max:500',
            ]);

            $order = Order::findOrFail($request->order_id);
            $order->update([
                'status' => 'confirmed',
                'delevery_date' => $request->expected_delivery_date,
                'note' => $request->delivery_notes,
            ]);

            // إطلاق الحدث لإرسال الإيميل
            $pharmacyEmail = 'matrex663@gmail.com'; // يمكن استبداله لاحقاً بإيميل الصيدلية من قاعدة البيانات
            event(new ConifermOrder($order->order_number, $request->expected_delivery_date, $pharmacyEmail));

            return redirect()->back()->with('Status_Update', 'تم قبول الطلبية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء قبول الطلبية');
        }
    }

    public function updateOrder(Request $request)
    {
        // dd($request->edit_notes);
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'quantities' => 'required|array',
                'quantities.*' => 'required|integer|min:1|max:1000',
                'prices' => 'required|array',
                'prices.*' => 'required|numeric|min:0.01|max:10000',
                'edit_notes' => 'nullable|string|max:1000',
            ]);

            $order = Order::findOrFail($request->order_id);
            
            // التحقق من أن الطلبية قابلة للتعديل
            if ($order->status !== 'pending') {
                return redirect()->back()->with('error', 'لا يمكن تعديل الطلبية في حالتها الحالية');
            }

            $totalAmount = 0;
            $changes = [];

            foreach ($request->quantities as $itemId => $quantity) {
                $orderItem = $order->orderItems()->find($itemId);
                if (!$orderItem) {
                    continue;
                }

                $price = $request->prices[$itemId];
                $originalQuantity = $orderItem->quantity;
                $originalPrice = $orderItem->unit_price;
                $newTotal = $quantity * $price;

                // تحديث بيانات المنتج
                $orderItem->update([
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $newTotal,
                ]);

                $totalAmount += $newTotal;

                // تسجيل التغييرات
                if ($quantity != $originalQuantity || $price != $originalPrice) {
                    $changes[] = [
                        'item' => $orderItem->medicine->medicine_name,
                        'old_quantity' => $originalQuantity,
                        'new_quantity' => $quantity,
                        'old_price' => $originalPrice,
                        'new_price' => $price,
                    ];
                }
            }

            // تحديث ملاحظات التعديل
            if ($request->edit_notes) {
                $order->note = ($order->note ? $order->note . "\n" : "") . "ملاحظات التعديل: " . $request->edit_notes;
                $order->save();
            }

            // إرسال إشعار للصيدلية بالتغييرات
            // يمكن إضافة كود إرسال الإشعار هنا

            return redirect()->back()->with('order_updated', 'تم تحديث الطلبية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الطلبية: ' . $e->getMessage());
        }
    }

    public function show_cancel_orders()
    {
        $supplier = auth()->user();
        $orders = Order::where('supplier_id', $supplier->id)
                      ->where('status', 'cancelled')
                      ->with('orderItems.medicine')
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('orders.show_canceled_orders', compact('orders', 'supplier'));
    }
}
