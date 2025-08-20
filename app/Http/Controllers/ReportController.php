<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\InventoryCount;
use App\Models\DamagedMedicine;
use App\Models\InventoryCountItem;
use App\Models\OrderItem;
use App\Models\MedicineBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // تم حذف تقرير الجرد الشامل القديم لعدم استخدامه

// =======================================================================================

// =======================================================================================

    /**
     * تقرير لحظي مبسّط للمخزون (KPIs + تفاصيل مختصرة)
     */
    public function inventorySnapshot(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'search' => 'nullable|string',
            'days' => 'nullable|numeric|min:1',
            'limit' => 'nullable|integer|min:1|max:200'
        ]);



        $days = (int) $request->input('days', 60);
        $limit = (int) $request->input('limit', 20);

        // قاعدة الاستعلام الرئيسية للأدوية
        $medicineBaseQuery = Medicine::with(['category'])
            ->when($request->category_id, function($q, $categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($request->search, function($q, $search) {
                $q->where(function($qq) use ($search) {
                    $qq->where('medicine_name', 'like', "%{$search}%")
                       ->orWhere('arabic_name', 'like', "%{$search}%")
                       ->orWhere('sentific_name', 'like', "%{$search}%")
                       ->orWhere('bar_code', 'like', "%{$search}%");
                });
            });

        $medicines = (clone $medicineBaseQuery)->get();

        // المؤشرات العامة
        $total_medicines = $medicines->count();
        $total_quantity = (int) $medicines->sum('quantity');
        $stock_value_cost = $medicines->sum(function($m) {
            return ((float) ($m->quantity ?? 0)) * ((float) ($m->supplier_price ?? 0));
        });
        $potential_revenue = $medicines->sum(function($m) {
            return ((float) ($m->quantity ?? 0)) * ((float) ($m->people_price ?? 0));
        });

        // منخفض المخزون
        $lowStockBaseQuery = (clone $medicineBaseQuery)
            ->whereColumn('quantity', '<=', 'alert_quantity')
            ->where('alert_quantity', '>', 0);

        $totalLowStockCount = (clone $lowStockBaseQuery)->count();

        $lowStock = (clone $lowStockBaseQuery)
            ->orderByRaw('(quantity - alert_quantity) asc')
            ->limit($limit)
            ->get()
            ->map(function($m) {
                return [
                    'medicine_id' => $m->id,
                    'medicine_name' => $m->medicine_name,
                    'category' => $m->category->name ?? null,
                    'quantity' => (int) $m->quantity,
                    'alert_quantity' => (int) $m->alert_quantity,
                    'supplier_price' => (float) ($m->supplier_price ?? 0),
                ];
            });

        // منقطع المخزون
        $out_of_stock_count = (clone $medicineBaseQuery)
            ->where('quantity', '=', 0)
            ->count();

        // أعلى أدوية من حيث قيمة المخزون
        $topValueItems = (clone $medicineBaseQuery)
            ->orderByRaw('(quantity * COALESCE(supplier_price, 0)) desc')
            ->limit($limit)
            ->get()
            ->map(function($m) {
                $stockCost = ((float) ($m->quantity ?? 0)) * ((float) ($m->supplier_price ?? 0));
                $stockRetail = ((float) ($m->quantity ?? 0)) * ((float) ($m->people_price ?? 0));
                return [
                    'medicine_id' => $m->id,
                    'medicine_name' => $m->medicine_name,
                    'category' => $m->category->name ?? null,
                    'quantity' => (int) $m->quantity,
                    'stock_cost' => $stockCost,
                    'stock_retail' => $stockRetail,
                ];
            });

        // قريب الانتهاء (حسب الدفعات)
        $expiringBatchQuery = MedicineBatch::with(['medicine.category'])
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<', now()->addDays($days))
            ->when($request->category_id, function($q, $categoryId) {
                $q->whereHas('medicine', function($mq) use ($categoryId) {
                    $mq->where('category_id', $categoryId);
                });
            })
            ->when($request->search, function($q, $search) {
                $q->whereHas('medicine', function($mq) use ($search) {
                    $mq->where(function($qq) use ($search) {
                        $qq->where('medicine_name', 'like', "%{$search}%")
                           ->orWhere('arabic_name', 'like', "%{$search}%")
                           ->orWhere('sentific_name', 'like', "%{$search}%")
                           ->orWhere('bar_code', 'like', "%{$search}%");
                    });
                });
            });

        $expiringBatches = $expiringBatchQuery->orderBy('expiry_date')->get();
        $totalExpiringBatchesCount = $expiringBatches->count();
        $expiringTotalCost = $expiringBatches->sum(function($b) {
            return ((float) ($b->quantity ?? 0)) * ((float) ($b->unit_price ?? 0));
        });

        $expiringGrouped = $expiringBatches->groupBy('medicine_id')->map(function($items) {
            $first = $items->first();
            $totalQty = (int) $items->sum('quantity');
            $totalCost = $items->sum(function($b) {
                return ((float) ($b->quantity ?? 0)) * ((float) ($b->unit_price ?? 0));
            });
            $batches = $items->map(function($b) {
                return [
                    'batch_number' => $b->batch_number,
                    'expiry_date' => optional($b->expiry_date)?->format('Y-m-d'),
                    'quantity' => (int) ($b->quantity ?? 0),
                    'unit_price' => (float) ($b->unit_price ?? 0),
                    'value_cost' => ((float) ($b->quantity ?? 0)) * ((float) ($b->unit_price ?? 0)),
                ];
            })->values();

            return [
                'medicine_id' => $first->medicine_id,
                'medicine_name' => $first->medicine->medicine_name ?? null,
                'category' => optional($first->medicine->category)->name,
                'total_batch_quantity' => $totalQty,
                'earliest_expiry' => optional($items->min('expiry_date'))?->format('Y-m-d'),
                'total_cost_value' => $totalCost,
                'batches' => $batches,
            ];
        })->values()->sortBy('earliest_expiry')->values()->take($limit);

        // تجميع حسب التصنيفات
        $byCategory = $medicines->groupBy(function($m) {
            return $m->category->name ?? 'غير مصنف';
        })->map(function($items, $category) {
            $quantity = (int) $items->sum('quantity');

            $cost = $items->sum(function($m) {
                return ((float) ($m->quantity ?? 0)) * ((float) ($m->supplier_price ?? 0));
            });
            $retail = $items->sum(function($m) {
                return ((float) ($m->quantity ?? 0)) * ((float) ($m->people_price ?? 0));
            });
            return [
                'category' => $category,

                'total_quantity' => $quantity,
                'stock_cost' => $cost,
                'stock_retail' => $retail,
            ];
        })->values()->sortByDesc('stock_cost')->values();

        // نسب تحليلية
        $lowStockPercentage = $total_medicines > 0 ? ($totalLowStockCount / $total_medicines) * 100 : 0;

        $response = [
            'kpis' => [
                'total_medicines' => $total_medicines,
                'total_quantity' => $total_quantity,
                'stock_value_cost' => $stock_value_cost,
                'potential_revenue' => $potential_revenue,
                'out_of_stock_count' => $out_of_stock_count,
                'low_stock_percentage' => $lowStockPercentage,
            ],
            'low_stock' => [
                'count' => $totalLowStockCount,
                'items' => $lowStock,
            ],
            'near_expiry' => [
                'days_window' => $days,
                'count' => $expiringGrouped->count(), // عدد الأدوية
                'batches_count' => $totalExpiringBatchesCount, // إجمالي الدُفعات ضمن النافذة
                'items' => $expiringGrouped,
            ],
            'by_category' => $byCategory,
            'top_value_items' => $topValueItems,
        ];

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تقرير المخزون اللحظي',
            'data' => $response,
        ]);
    }

// =======================================================================================

// =======================================================================================


    public function ExpiryReports(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'medicine_name' => 'nullable|string',
            'days' => 'nullable|numeric',
            'export' => 'nullable|in:pdf'
        ]);
        $days =(int)$request->input('days', 60);
        $today = now()->startOfDay();

        $query = OrderItem::with(['medicine.category'])
            ->where('expiry_date', '<', now()->addDays($days))
            ->where('quantity', '>', 0)
            ->whereHas('medicine', function($q) {
                $q->where('quantity', '>', 0);
            });

        if ($request->category_id) {
            $query->whereHas('medicine', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        if ($request->medicine_name) {
            $query->whereHas('medicine', function($q) use ($request) {
                $q->where('medicine_name', 'like', '%' . $request->medicine_name . '%');
            });
        }

        $orderItems = $query->orderBy('expiry_date')->get();

        $expired = [];
        $expiring_soon = [];
        $total_expired_value = 0;
        $total_expiring_soon_value = 0;

        foreach ($orderItems as $item) {
            $days_to_expiry = $today->diffInDays($item->expiry_date, false);
            $row = [
                'medicine_name' => $item->medicine->medicine_name,
                'category' => $item->medicine->category->name ?? null,
                'quantity' => $item->medicine->quantity,
                'expiry_date' => $item->expiry_date,
                'total_value' => $item->medicine->quantity * $item->unit_price,
            ];
            if ($item->expiry_date < $today) {
                // منتهي الصلاحية: لا تعرض days_to_expiry أو اجعلها "-"
                $row['days_to_expiry'] = '-';
                $expired[] = $row;
                $total_expired_value += $row['total_value'];
            } else {
                // لم ينته بعد: days_to_expiry موجبة
                $row['days_to_expiry'] = $days_to_expiry;
                $row['is_urgent'] = $days_to_expiry >= 0 && $days_to_expiry < 7;
                $expiring_soon[] = $row;
                $total_expiring_soon_value += $row['total_value'];
            }
        }

        // ترتيب القريب الانتهاء حسب الأيام المتبقية
        usort($expiring_soon, function($a, $b) {
            return $a['days_to_expiry'] <=> $b['days_to_expiry'];
        });

        $response = [
            'expired' => [
                'count' => count($expired),
                'total_value' => $total_expired_value,
                'items' => $expired
            ],
            'expiring_soon' => [
                'count' => count($expiring_soon),
                'total_value' => $total_expiring_soon_value,
                'items' => $expiring_soon
            ]
        ];

        // تصدير PDF فعلي باستخدام القالب الإنجليزي
        if ($request->export === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.expiry', ['data' => $response])
                ->setPaper('A4', 'portrait');
            return $pdf->download('expiry_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return response()->json([
            'status' => true,
            'data' => $response
        ]);
    }



    public function talif_report(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|in:damaged,expired,storage_issue',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date'
        ]);

        $query = DamagedMedicine::with(['batch.medicine.category'])
            ->whereHas('batch');

        // فلترة حسب السبب
        if ($request->reason) {
            $query->where('reason', $request->reason);
        }

        // فلترة حسب التاريخ
        if ($request->from_date) {
            $query->where('damaged_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->where('damaged_at', '<=', $request->to_date);
        }

        $damaged = $query->orderBy('damaged_at', 'desc')->get();

        // دالة مساعدة لحساب قيمة التلف مع تفضيل سعر الدُفعة
        $calcValue = function($item) {
            $unitPrice = optional($item->batch)->unit_price;
            if ($unitPrice === null) {
                $unitPrice = optional(optional($item->batch)->medicine)->supplier_price;
            }
            $unitPrice = (float) ($unitPrice ?? 0);
            return ((float) ($item->quantity_talif ?? 0)) * $unitPrice;
        };

        // حساب الإحصائيات الأساسية
        $total_damaged_count = $damaged->count();
        $total_damaged_quantity = $damaged->sum('quantity_talif');
        $total_value_loss = $damaged->sum($calcValue);

        // تجميع حسب السبب
        $by_reason = $damaged->groupBy('reason')->map(function($items, $reason) use ($calcValue) {
            return [
                'count' => $items->count(),
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum($calcValue),
            ];
        });

        // الاتجاهات الشهرية
        $monthly_trends = $damaged->groupBy(function($item) {
            return $item->damaged_at->format('Y-m');
        })->map(function($items, $month) use ($calcValue) {
            return [
                'month' => $month,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum($calcValue),
            ];
        })->sortBy('month')->values();

        // أعلى الأدوية من حيث قيمة التلف (اعتماداً على الدُفعات)
        $top_damaged_medicines = $damaged->groupBy(function($item) {
            return optional($item->batch)->medicine_id;
        })->map(function($items, $medicineId) use ($calcValue) {
            $medicine = optional($items->first()->batch)->medicine;
            $most_common_reason = $items->groupBy('reason')->sortByDesc(function($group) {
                return $group->sum('quantity_talif');
            })->keys()->first();

            return [
                'medicine_id' => $medicineId,
                'medicine_name' => optional($medicine)->medicine_name,
                'category' => optional($medicine->category)->name,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum($calcValue),
                'reason' => $most_common_reason
            ];
        })->sortByDesc('value')->take(6)->values();

        // تجميع حسب الفئة (اعتماداً على دُفعات الأدوية)
        $by_category = $damaged->groupBy(function($item) {
            return optional(optional($item->batch)->medicine->category)->name ?? 'غير مصنف';
        })->map(function($items, $category) use ($calcValue) {
            return [
                'category' => $category,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum($calcValue),
            ];
        })->sortByDesc('value')->values();

        // تفاصيل كل حالة تلف مع معلومات الدُفعة
        $details = $damaged->map(function($item) use ($calcValue) {
            $batch = $item->batch;
            $medicine = optional($batch)->medicine;
            return [
                'batch_number' => optional($batch)->batch_number,
                'batch_expiry_date' => optional(optional($batch)->expiry_date)?->format('Y-m-d'),
                'batch_unit_price' => (float) (optional($batch)->unit_price ?? 0),
                'medicine_name' => optional($medicine)->medicine_name,
                'category' => optional(optional($medicine)->category)->name,
                'quantity_talif' => $item->quantity_talif,
                'reason' => $item->reason,
                'damaged_at' => $item->damaged_at->format('Y-m-d'),
                'value_loss' => $calcValue($item),
            ];
        });

        // التوصيات الذكية (تُحسب اعتماداً على الدُفعات)
        $recommendations = [];
        $expiredCount = ($by_reason['expired']['count'] ?? $by_reason->get('expired')['count'] ?? 0);
        if ($total_damaged_count > 0 && $expiredCount > ($total_damaged_count * 0.5)) {
            $recommendations[] = 'تعزيز مراقبة تواريخ الانتهاء وتطبيق مبدأ FIFO.';
        }

        $storageIssueCount = ($by_reason['storage_issue']['count'] ?? $by_reason->get('storage_issue')['count'] ?? 0);
        if ($total_damaged_count > 0 && $storageIssueCount > ($total_damaged_count * 0.3)) {
            $recommendations[] = 'مراجعة وتحسين شروط التخزين ودرجة الحرارة.';
        }

        if ($top_damaged_medicines->count() > 0) {
            $top_medicine = $top_damaged_medicines->first();
            if (!empty($top_medicine['medicine_name'])) {
                $recommendations[] = 'التركيز على تحسين تداول: ' . $top_medicine['medicine_name'];
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'مراقبة اتجاهات التلف بصورة دورية وتحسين الإجراءات الوقائية.';
        }

        $response = [
            'status' => true,
            'summary' => [
                'total_damaged_count' => $total_damaged_count,
                'total_damaged_quantity' => $total_damaged_quantity,
                'total_value_loss' => $total_value_loss,
                'by_reason' => $by_reason
            ],
            'monthly_trends' => $monthly_trends,
            'top_damaged_medicines' => $top_damaged_medicines,
            'by_category' => $by_category,
            'details' => $details,
            'recommendations' => $recommendations
        ];

        return response()->json($response);
    }

    /**
     * تقرير تحليل التصنيفات (مُعاد تفعيله)
     */
    public function categoryAnalysisReport(Request $request)
    {
        $request->validate([
            'brand_id' => 'nullable|exists:brands,id',
            'medicine_form_id' => 'nullable|exists:medicine_forms,id',
            'search' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $limit = (int) $request->input('limit', 10);

        // dd($request->all());

        // تحميل التصنيفات مع أدويةها وعلاقاتها المطلوبة
        $categories = \App\Models\Category::with(['medicines.category', 'medicines.medicineForm', 'medicines.brand'])->get();

        $data = $categories->map(function($category) use ($request, $limit) {
            $meds = $category->medicines;

            // فلاتر اختيارية على مستوى الأدوية داخل التصنيف
            if ($request->brand_id) {
                $meds = $meds->where('brand_id', (int) $request->brand_id);
            }
            if ($request->medicine_form_id) {
                $meds = $meds->where('medicine_form_id', (int) $request->medicine_form_id);
            }
            if ($request->search) {
                $search = strtolower($request->search);
                $meds = $meds->filter(function($m) use ($search) {
                    $name = strtolower($m->medicine_name ?? '');
                    $an = strtolower($m->arabic_name ?? '');
                    $sn = strtolower($m->sentific_name ?? '');
                    $bc = strtolower($m->bar_code ?? '');
                    return str_contains($name, $search)
                        || str_contains($an, $search)
                        || str_contains($sn, $search)
                        || str_contains($bc, $search);
                });
            }

            $totalMedicines = $meds->count();
            $totalQuantity = (int) $meds->sum('quantity');
            $stockCost = $meds->sum(function($m) {
                return ((float) ($m->quantity ?? 0)) * ((float) ($m->supplier_price ?? 0));
            });
            $stockRetail = $meds->sum(function($m) {
                return ((float) ($m->quantity ?? 0)) * ((float) ($m->people_price ?? 0));
            });
            $lowStockCount = $meds->filter(function($m) {
                return (int) ($m->alert_quantity ?? 0) > 0 && (int) ($m->quantity ?? 0) <= (int) ($m->alert_quantity ?? 0);
            })->count();
            $outOfStockCount = $meds->where('quantity', 0)->count();
            $avgSupplierPrice = $totalMedicines > 0 ? (float) $meds->avg('supplier_price') : 0.0;
            $avgPeoplePrice = $totalMedicines > 0 ? (float) $meds->avg('people_price') : 0.0;

            // توزيع حسب الشكل الدوائي
            $formsDistribution = $meds->groupBy('medicine_form_id')->map(function($items, $formId) {
                $formName = optional($items->first()->medicineForm)->name ?? 'غير محدد';
                return [
                    'form_id' => $formId,
                    'form_name' => $formName,
                    'count' => $items->count(),
                ];
            })->values()->sortByDesc('count')->values();

            // توزيع حسب البراند
            $brandsDistribution = $meds->groupBy('brand_id')->map(function($items, $brandId) {
                $brandName = optional($items->first()->brand)->name ?? 'غير محدد';
                return [
                    'brand_id' => $brandId,
                    'brand_name' => $brandName,
                    'count' => $items->count(),
                ];
            })->values()->sortByDesc('count')->values();

            // أعلى الأدوية قيمة داخل التصنيف
            $topMedicinesByValue = $meds->map(function($m) {
                $cost = ((float) ($m->quantity ?? 0)) * ((float) ($m->supplier_price ?? 0));
                $retail = ((float) ($m->quantity ?? 0)) * ((float) ($m->people_price ?? 0));
                return [
                    'medicine_id' => $m->id,
                    'medicine_name' => $m->medicine_name,
                    'form' => optional($m->medicineForm)->name,
                    'brand' => optional($m->brand)->name,
                    'quantity' => (int) $m->quantity,
                    'supplier_price' => (float) ($m->supplier_price ?? 0),
                    'people_price' => (float) ($m->people_price ?? 0),
                    'stock_cost' => $cost,
                    'stock_retail' => $retail,
                ];
            })->sortByDesc('stock_cost')->values()->take($limit);

            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'totals' => [
                    'total_medicines' => $totalMedicines,
                    'total_quantity' => $totalQuantity,
                    'stock_cost' => $stockCost,
                    'stock_retail' => $stockRetail,
                    'low_stock_count' => $lowStockCount,
                    'out_of_stock_count' => $outOfStockCount,
                    'avg_supplier_price' => $avgSupplierPrice,
                    'avg_people_price' => $avgPeoplePrice,
                ],
                'distributions' => [
                    'forms' => $formsDistribution,
                    'brands' => $brandsDistribution,
                ],
                'top_medicines_by_value' => $topMedicinesByValue,
            ];
        })->values();

        // ملخص شامل عبر كل التصنيفات
        $summary = [
            'categories_count' => $data->count(),
            'total_medicines' => (int) $data->sum(fn($c) => $c['totals']['total_medicines']),
            'total_quantity' => (int) $data->sum(fn($c) => $c['totals']['total_quantity']),
            'stock_cost' => (float) $data->sum(fn($c) => $c['totals']['stock_cost']),
            'stock_retail' => (float) $data->sum(fn($c) => $c['totals']['stock_retail']),
            'low_stock_count' => (int) $data->sum(fn($c) => $c['totals']['low_stock_count']),
            'out_of_stock_count' => (int) $data->sum(fn($c) => $c['totals']['out_of_stock_count']),
        ];

        return response()->json([
            'status' => true,
            'message' => 'تحليل التصنيفات',
            'summary' => $summary,
            'data' => $data,
        ]);
    }

}
