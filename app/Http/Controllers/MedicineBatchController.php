<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicineBatch;

class MedicineBatchController extends Controller
{
    /**
     * عرض جميع الدفعات مع إمكانية الفلترة
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'nullable|exists:medicines,id',
            'is_active' => 'nullable|boolean',
            'search' => 'nullable|string|max:255',
        ]);

        $query = MedicineBatch::with('medicine');

        if (!empty($validated['medicine_id'])) {
            $query->where('medicine_id', $validated['medicine_id']);
        }
        if (isset($validated['is_active'])) {
            $query->where('is_active', $validated['is_active']);
        }
        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('medicine', function($qq) use ($search) {
                      $qq->where('medicine_name', 'like', "%{$search}%")
                         ->orWhere('bar_code', 'like', "%{$search}%");
                  });
            });
        }

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(15);

        $batches->getCollection()->transform(function($batch) {
            return [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => $batch->quantity,
                'expiry_date' => $batch->expiry_date,
                'unit_price' => $batch->unit_price,
                'is_active' => $batch->is_active,
                'medicine' => $batch->medicine ? [
                    'id' => $batch->medicine->id,
                    'name' => $batch->medicine->medicine_name,
                    'barcode' => $batch->medicine->bar_code
                ] : null
            ];
        });

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الدفعات بنجاح',
            'data' => $batches->items(),
            'meta' => [
                'current_page' => $batches->currentPage(),
                'last_page' => $batches->lastPage(),
                'total' => $batches->total()
            ]
        ]);
    }

    /**
     * عرض تفاصيل دفعة واحدة
     */
    public function show($id)
    {
        $batch = MedicineBatch::with('medicine')->find($id);
        if (!$batch) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'لم يتم العثور على الدفعة'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب تفاصيل الدفعة بنجاح',
            'data' => [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => $batch->quantity,
                'expiry_date' => $batch->expiry_date,
                'unit_price' => $batch->unit_price,
                'is_active' => $batch->is_active,
                'medicine' => $batch->medicine ? [
                    'id' => $batch->medicine->id,
                    'name' => $batch->medicine->medicine_name,
                    'barcode' => $batch->medicine->bar_code,
                    'type' => $batch->medicine->type,
                    'alert_quantity' => $batch->medicine->alert_quantity
                ] : null
            ]
        ]);
    }
}
