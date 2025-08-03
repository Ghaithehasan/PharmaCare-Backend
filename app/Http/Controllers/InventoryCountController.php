<?php

namespace App\Http\Controllers;

use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InventoryCountController extends Controller
{
    // إنشاء عملية جرد جديدة
    public function store(Request $request)
    {
        // dd();
        $request->validate([
            'count_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء عملية الجرد
            $inventoryCount = InventoryCount::create([
                'count_date' => $request->count_date,
                'count_number' => 'INV-' . Str::random(8),
                'status' => InventoryCount::STATUS_IN_PROGRESS,
                'notes' => $request->notes,
                // 'created_by' => auth()->id()
                'created_by' => 1
            ]);

            // إضافة تفاصيل الجرد
            foreach ($request->items as $item) {
                $medicine = Medicine::find($item['medicine_id']);

                InventoryCountItem::create([
                    'inventory_count_id' => $inventoryCount->id,
                    'medicine_id' => $item['medicine_id'],
                    'system_quantity' => $medicine->quantity,
                    'actual_quantity' => $item['actual_quantity'],
                    'difference' => $item['actual_quantity'] - $medicine->quantity,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء عملية الجرد بنجاح',
                'data' => $inventoryCount->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء عملية الجرد',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // تحديث حالة عملية الجرد
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . InventoryCount::STATUS_IN_PROGRESS . ',' . InventoryCount::STATUS_COMPLETED
        ]);

        $inventoryCount = InventoryCount::findOrFail($id);

        // التحقق من أن الجرد لم يكتمل بعد
        if ($inventoryCount->isCompleted()) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن تغيير حالة عملية الجرد المكتملة'
            ], 400);
        }

        if ($request->status === InventoryCount::STATUS_COMPLETED) {
            DB::beginTransaction();
            try {
                foreach ($inventoryCount->items as $item) {
                    $medicine = Medicine::find($item->medicine_id);

                    if (!$medicine) {
                        throw new \Exception("الدواء غير موجود: ID {$item->medicine_id}");
                    }

                    $medicine->quantity = $item->actual_quantity;
                    $medicine->save();
                }

                // $inventoryCount->approved_by = auth()->id();
                $inventoryCount->status = InventoryCount::STATUS_COMPLETED;
                $inventoryCount->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'تم إكمال عملية الجرد بنجاح',
                    'data' => $inventoryCount->load('items')
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'حدث خطأ أثناء إكمال عملية الجرد',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        $inventoryCount->status = $request->status;
        $inventoryCount->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث حالة عملية الجرد بنجاح',
            'data' => $inventoryCount->load('items')
        ]);
    }

    // عرض تفاصيل عملية جرد
    public function show($id)
    {
        $inventoryCount = InventoryCount::with(['items.medicine', 'createdBy'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $inventoryCount
        ]);
    }

    // قائمة عمليات الجرد
    public function index()
    {
        $inventoryCounts = InventoryCount::query()
            ->with([
                'createdBy:id,name',
                'items' => function ($query) {
                    $query->select('id', 'inventory_count_id', 'medicine_id', 'system_quantity', 'actual_quantity', 'difference')
                        ->with(['medicine:id,medicine_name,bar_code']);
                }
            ])
            ->select([
                'id',
                'count_number',
                'count_date',
                'status',
                'created_by',
                'created_at'
            ])
            ->latest()
            ->paginate(10);

        // تحويل البيانات إلى الشكل المطلوب
        $formattedData = $inventoryCounts->through(function ($count) {
            return [
                'id' => $count->id,
                'count_number' => $count->count_number,
                'count_date' => $count->count_date,
                'status' => $count->status,
                'created_by' => $count->createdBy->name ,
                'approved_by' => $count->approvedBy ? $count->approvedBy->name : null,
                'items_count' => $count->items->count(),
                'created_at' => $count->created_at->format('Y-m-d H:i:s'),
                'items' => $count->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'medicine' => [
                            'id' => $item->medicine->id,
                            'name' => $item->medicine->medicine_name,
                            'bar_code' => $item->medicine->bar_code
                        ],
                        'system_quantity' => $item->system_quantity,
                        'actual_quantity' => $item->actual_quantity,
                        'difference' => $item->difference
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'inventory_counts' => $formattedData->items(),
                'pagination' => [
                    'total' => $formattedData->total(),
                    'per_page' => $formattedData->perPage(),
                    'current_page' => $formattedData->currentPage(),
                    'last_page' => $formattedData->lastPage(),
                    'from' => $formattedData->firstItem(),
                    'to' => $formattedData->lastItem(),
                    'has_more_pages' => $formattedData->hasMorePages()
                ]
            ]
        ]);
    }
}