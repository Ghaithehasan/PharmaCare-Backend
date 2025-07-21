<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\InventoryCount;
use App\Models\DamagedMedicine;
use App\Models\InventoryCountItem;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * تقرير شامل لجرد المخزون
     */
    public function comprehensiveInventoryReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:in_progress,completed'
        ]);

        $query = InventoryCount::with(['items.medicine.category', 'createdBy', 'approvedBy']);

        if ($request->start_date) {
            $query->where('count_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('count_date', '<=', $request->end_date);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $inventoryCounts = $query->orderBy('count_date', 'desc')->get();


        $report = [
            'summary' => $this->generateInventorySummary($inventoryCounts),
            'discrepancies' => $this->analyzeDiscrepancies($inventoryCounts),
            'category_analysis' => $this->categoryDiscrepancyAnalysis($inventoryCounts),
            'trends' => $this->generateTrends($inventoryCounts),
            'recommendations' => $this->generateRecommendations($inventoryCounts)
        ];

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء التقرير الشامل بنجاح',
            'data' => $report
        ]);
    }



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

        $query = DamagedMedicine::with(['medicine.category']);

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

        // حساب الإحصائيات الأساسية
        $total_damaged_count = $damaged->count();
        $total_damaged_quantity = $damaged->sum('quantity_talif');
        $total_value_loss = $damaged->sum(function($item) {
            return $item->quantity_talif * ($item->medicine->supplier_price ?? 0);
        });

        // تجميع حسب السبب
        $by_reason = $damaged->groupBy('reason')->map(function($items, $reason) {
            return [
                'count' => $items->count(),
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum(function($item) {
                    return $item->quantity_talif * ($item->medicine->supplier_price ?? 0);
                }),
            ];
        });

        // الاتجاهات الشهرية
        $monthly_trends = $damaged->groupBy(function($item) {
            return $item->damaged_at->format('Y-m');
        })->map(function($items, $month) {
            return [
                'month' => $month,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum(function($item) {
                    return $item->quantity_talif * ($item->medicine->supplier_price ?? 0);
                }),
            ];
        })->sortBy('month')->values();

        $top_damaged_medicines = $damaged->groupBy('medicine_id')->map(function($items, $medicine_id) {
            $medicine = $items->first()->medicine;
            $most_common_reason = $items->groupBy('reason')->sortByDesc(function($group) {
                return $group->sum('quantity_talif');
            })->keys()->first();

            return [
                'medicine_name' => $medicine->medicine_name,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum(function($item) {
                    return $item->quantity_talif * ($item->medicine->supplier_price ?? 0);
                }),
                'reason' => $most_common_reason
            ];
        })->sortByDesc('value')->take(6)->values();

        // تجميع حسب الفئة
        $by_category = $damaged->groupBy('medicine.category.name')->map(function($items, $category) {
            return [
                'category' => $category,
                'quantity' => $items->sum('quantity_talif'),
                'value' => $items->sum(function($item) {
                    return $item->quantity_talif * ($item->medicine->supplier_price ?? 0);
                }),
            ];
        })->sortByDesc('value')->values();

        // تفاصيل كل حالة تلف
        $details = $damaged->map(function($item) {
            return [
                'medicine_name' => $item->medicine->medicine_name,
                'category' => $item->medicine->category->name ?? null,
                'quantity_talif' => $item->quantity_talif,
                'reason' => $item->reason,
                'notes' => $item->notes,
                'damaged_at' => $item->damaged_at->format('Y-m-d'),
                'value_loss' => $item->quantity_talif * ($item->medicine->supplier_price ?? 0)
            ];
        });

        // التوصيات الذكية
        $recommendations = [];

        // توصية إذا كان التلف بسبب انتهاء الصلاحية أكثر من 50%
        $expired_percentage = $by_reason->get('expired')['count'] ?? 0;
        if ($expired_percentage > ($total_damaged_count * 0.5)) {
            $recommendations[] = "Improve expiry date monitoring and stock rotation.";
        }

        // توصية إذا كان التلف بسبب سوء التخزين أكثر من 30%
        $storage_percentage = $by_reason->get('storage_issue')['count'] ?? 0;
        if ($storage_percentage > ($total_damaged_count * 0.3)) {
            $recommendations[] = "Review and improve storage conditions.";
        }

        // توصية إذا كان هناك أدوية تتلف بشكل متكرر
        if ($top_damaged_medicines->count() > 0) {
            $top_medicine = $top_damaged_medicines->first();
            $recommendations[] = "Focus on improving handling of {$top_medicine['medicine_name']}.";
        }

        // إذا لم تكن هناك توصيات محددة
        if (empty($recommendations)) {
            $recommendations[] = "Monitor damaged medicines trends regularly.";
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
     * تقرير الفروقات والتناقضات
     */
    public function discrepancyReport(Request $request)
    {
        $request->validate([
            'threshold' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $threshold = $request->threshold ?? 5; // نسبة الفرق المسموح بها

        $discrepancies = InventoryCountItem::with(['inventoryCount', 'medicine.category'])
            ->where('difference', '!=', 0)
            ->whereRaw('ABS(difference) >= (system_quantity * ? / 100)', [$threshold])
            ->when($request->category_id, function($query, $categoryId) {
                return $query->whereHas('medicine', function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            })
            ->orderByRaw('ABS(difference) DESC')
            ->get();

        $analysis = [
            'critical_discrepancies' => $discrepancies->where('difference', '<', -10),
            'moderate_discrepancies' => $discrepancies->whereBetween('difference', [-10, -5]),
            'minor_discrepancies' => $discrepancies->whereBetween('difference', [-5, 0]),
            'overstock_discrepancies' => $discrepancies->where('difference', '>', 0),
            'statistics' => [
                'total_discrepancies' => $discrepancies->count(),
                'total_value_loss' => $discrepancies->sum(function($item) {
                    return abs($item->difference) * $item->medicine->supplier_price;
                }),
                'average_discrepancy_percentage' => $discrepancies->avg(function($item) {
                    return $item->system_quantity > 0 ? (abs($item->difference) / $item->system_quantity) * 100 : 0;
                })
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'تقرير الفروقات والتناقضات',
            'data' => $analysis
        ]);
    }

    /**
     * تقرير الأدوية المفقودة والمتسربة
     */
    public function missingAndLeakageReport()
    {
        $missingItems = InventoryCountItem::with(['inventoryCount', 'medicine.category'])
            ->where('difference', '<', 0)
            ->where('actual_quantity', 0)
            ->orderBy('difference', 'asc')
            ->get();

        $leakageItems = InventoryCountItem::with(['inventoryCount', 'medicine.category'])
            ->where('difference', '<', 0)
            ->where('actual_quantity', '>', 0)
            ->orderBy('difference', 'asc')
            ->get();

        $report = [
            'missing_items' => [
                'count' => $missingItems->count(),
                'total_value' => $missingItems->sum(function($item) {
                    return abs($item->difference) * $item->medicine->supplier_price;
                }),
                'items' => $missingItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine->medicine_name,
                        'category' => $item->medicine->category->name,
                        'missing_quantity' => abs($item->difference),
                        'value_loss' => abs($item->difference) * $item->medicine->supplier_price,
                        'count_date' => $item->inventoryCount->count_date
                    ];
                })
            ],
            'leakage_items' => [
                'count' => $leakageItems->count(),
                'total_value' => $leakageItems->sum(function($item) {
                    return abs($item->difference) * $item->medicine->supplier_price;
                }),
                'items' => $leakageItems->map(function($item) {
                    return [
                        'medicine_name' => $item->medicine->medicine_name,
                        'category' => $item->medicine->category->name,
                        'leaked_quantity' => abs($item->difference),
                        'value_loss' => abs($item->difference) * $item->medicine->supplier_price,
                        'leakage_percentage' => $item->system_quantity > 0 ?
                            (abs($item->difference) / $item->system_quantity) * 100 : 0
                    ];
                })
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'تقرير الأدوية المفقودة والمتسربة',
            'data' => $report
        ]);
    }

    /**
     * تقرير الأداء الزمني للجرد
     */
    public function timePerformanceReport()
    {
        $inventoryCounts = InventoryCount::with(['createdBy', 'approvedBy'])
            ->where('status', 'completed')
            ->orderBy('count_date', 'desc')
            ->get();

        $performance = [
            'total_counts' => $inventoryCounts->count(),
            'average_items_per_count' => $inventoryCounts->avg(function($count) {
                return $count->items->count();
            }),
            'completion_time_analysis' => $inventoryCounts->map(function($count) {
                $createdAt = Carbon::parse($count->created_at);
                $updatedAt = Carbon::parse($count->updated_at);
                $duration = $createdAt->diffInHours($updatedAt);

                return [
                    'count_number' => $count->count_number,
                    'count_date' => $count->count_date,
                    'duration_hours' => $duration,
                    'items_count' => $count->items->count(),
                    'efficiency_score' => $count->items->count() > 0 ?
                        $count->items->count() / $duration : 0
                ];
            }),
            'efficiency_ranking' => $inventoryCounts->map(function($count) {
                $duration = Carbon::parse($count->created_at)->diffInHours(Carbon::parse($count->updated_at));
                $itemsCount = $count->items->count();
                $efficiency = $itemsCount > 0 ? $itemsCount / $duration : 0;

                return [
                    'count_number' => $count->count_number,
                    'efficiency_score' => $efficiency,
                    'items_per_hour' => $efficiency
                ];
            })->sortByDesc('efficiency_score')->values()
        ];

        return response()->json([
            'status' => true,
            'message' => 'تقرير الأداء الزمني للجرد',
            'data' => $performance
        ]);
    }

    /**
     * تقرير تحليل التصنيفات
     */
    public function categoryAnalysisReport()
    {
        $categoryAnalysis = DB::table('inventory_count_items')
            ->join('medicines', 'inventory_count_items.medicine_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name as category_name',
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(ABS(inventory_count_items.difference)) as total_discrepancy'),
                DB::raw('AVG(ABS(inventory_count_items.difference)) as avg_discrepancy'),
                DB::raw('SUM(CASE WHEN inventory_count_items.difference < 0 THEN 1 ELSE 0 END) as missing_items'),
                DB::raw('SUM(CASE WHEN inventory_count_items.difference > 0 THEN 1 ELSE 0 END) as overstock_items'),
                DB::raw('SUM(ABS(inventory_count_items.difference) * medicines.supplier_price) as total_value_loss')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_value_loss', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تحليل التصنيفات',
            'data' => $categoryAnalysis
        ]);
    }

    /**
     * تقرير التنبؤات والتحليل المستقبلي
     */
    public function predictiveAnalysisReport()
    {
        // تحليل الاتجاهات الشهرية
        $monthlyTrends = DB::table('inventory_count_items')
            ->join('inventory_counts', 'inventory_count_items.inventory_count_id', '=', 'inventory_counts.id')
            ->select(
                DB::raw('YEAR(count_date) as year'),
                DB::raw('MONTH(count_date) as month'),
                DB::raw('COUNT(*) as total_items'),
                DB::raw('SUM(ABS(difference)) as total_discrepancy'),
                DB::raw('AVG(ABS(difference)) as avg_discrepancy')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // تحليل الأدوية الأكثر عرضة للفقدان
        $highRiskItems = DB::table('inventory_count_items')
            ->join('medicines', 'inventory_count_items.medicine_id', '=', 'medicines.id')
            ->select(
                'medicines.id',
                'medicines.medicine_name',
                DB::raw('COUNT(*) as discrepancy_count'),
                DB::raw('AVG(ABS(difference)) as avg_discrepancy'),
                DB::raw('SUM(CASE WHEN difference < 0 THEN 1 ELSE 0 END) as missing_occurrences')
            )
            ->groupBy('medicines.id', 'medicines.medicine_name')
            ->having('discrepancy_count', '>=', 2)
            ->orderBy('missing_occurrences', 'desc')
            ->limit(20)
            ->get();

        $predictions = [
            'monthly_trends' => $monthlyTrends,
            'high_risk_items' => $highRiskItems,
            'recommendations' => [
                'increase_security_for_high_risk_items' => $highRiskItems->where('missing_occurrences', '>=', 3)->count(),
                'implement_regular_audits' => $monthlyTrends->avg('avg_discrepancy') > 5,
                'consider_automated_tracking' => $highRiskItems->count() > 10
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'التحليل التنبؤي',
            'data' => $predictions
        ]);
    }

    /**
     * إنشاء تقرير PDF شامل
     */
    public function generatePDFReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:comprehensive,discrepancy,missing,performance,category,predictive'
        ]);

        $data = [];
        $report_type = $request->report_type;

        switch ($report_type) {
            case 'comprehensive':
                $inventoryCounts = InventoryCount::with(['items.medicine.category', 'createdBy', 'approvedBy'])->get();
                $data = [
                    'summary' => $this->generateInventorySummary($inventoryCounts),
                    'discrepancies' => $this->analyzeDiscrepancies($inventoryCounts),
                    'category_analysis' => $this->categoryDiscrepancyAnalysis($inventoryCounts),
                    'trends' => $this->generateTrends($inventoryCounts),
                    'recommendations' => $this->generateRecommendations($inventoryCounts)
                ];
                break;
            case 'discrepancy':
                $discrepancies = InventoryCountItem::with(['inventoryCount', 'medicine.category'])
                    ->where('difference', '!=', 0)
                    ->get();
                $data = [
                    'critical_discrepancies' => $discrepancies->where('difference', '<', -10),
                    'moderate_discrepancies' => $discrepancies->whereBetween('difference', [-10, -5]),
                    'minor_discrepancies' => $discrepancies->whereBetween('difference', [-5, 0]),
                    'overstock_discrepancies' => $discrepancies->where('difference', '>', 0)
                ];
                break;
            case 'missing':
                $missingItems = InventoryCountItem::with(['inventoryCount', 'medicine.category'])
                    ->where('difference', '<', 0)
                    ->where('actual_quantity', 0)
                    ->get();
                $data = [
                    'missing_items' => $missingItems,
                    'total_value' => $missingItems->sum(function($item) {
                        return abs($item->difference) * $item->medicine->supplier_price;
                    })
                ];
                break;
            case 'performance':
                $inventoryCounts = InventoryCount::with(['createdBy', 'approvedBy'])
                    ->where('status', 'completed')
                    ->get();
                $data = [
                    'total_counts' => $inventoryCounts->count(),
                    'average_items_per_count' => $inventoryCounts->avg(function($count) {
                        return $count->items->count();
                    }),
                    'efficiency_ranking' => $inventoryCounts->map(function($count) {
                        $duration = Carbon::parse($count->created_at)->diffInHours(Carbon::parse($count->updated_at));
                        $itemsCount = $count->items->count();
                        $efficiency = $itemsCount > 0 ? $itemsCount / $duration : 0;

                        return [
                            'count_number' => $count->count_number,
                            'efficiency_score' => $efficiency,
                            'items_per_hour' => $efficiency
                        ];
                    })->sortByDesc('efficiency_score')->values()
                ];
                break;
            case 'category':
                $categoryAnalysis = DB::table('inventory_count_items')
                    ->join('medicines', 'inventory_count_items.medicine_id', '=', 'medicines.id')
                    ->join('categories', 'medicines.category_id', '=', 'categories.id')
                    ->select(
                        'categories.id',
                        'categories.name as category_name',
                        DB::raw('COUNT(*) as total_items'),
                        DB::raw('SUM(ABS(inventory_count_items.difference)) as total_discrepancy'),
                        DB::raw('AVG(ABS(inventory_count_items.difference)) as avg_discrepancy'),
                        DB::raw('SUM(CASE WHEN inventory_count_items.difference < 0 THEN 1 ELSE 0 END) as missing_items'),
                        DB::raw('SUM(CASE WHEN inventory_count_items.difference > 0 THEN 1 ELSE 0 END) as overstock_items'),
                        DB::raw('SUM(ABS(inventory_count_items.difference) * medicines.supplier_price) as total_value_loss')
                    )
                    ->groupBy('categories.id', 'categories.name')
                    ->orderBy('total_value_loss', 'desc')
                    ->get();
                $data = ['category_analysis' => $categoryAnalysis];
                break;
            case 'predictive':
                $monthlyTrends = DB::table('inventory_count_items')
                    ->join('inventory_counts', 'inventory_count_items.inventory_count_id', '=', 'inventory_counts.id')
                    ->select(
                        DB::raw('YEAR(count_date) as year'),
                        DB::raw('MONTH(count_date) as month'),
                        DB::raw('COUNT(*) as total_items'),
                        DB::raw('SUM(ABS(difference)) as total_discrepancy'),
                        DB::raw('AVG(ABS(difference)) as avg_discrepancy')
                    )
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();
                $data = ['monthly_trends' => $monthlyTrends];
                break;
        }

        $pdf = Pdf::loadView('reports.inventory', compact('data', 'report_type'))
                  ->setPaper('A4', 'portrait');

        return $pdf->download('inventory_report_' . $report_type . '_' . date('Y-m-d') . '.pdf');
    }

    // Helper methods for data preparation
    private function generateInventorySummary($inventoryCounts)
    {
        $totalItems = $inventoryCounts->sum(function($count) {
            return $count->items->count();
        });

        $totalDiscrepancies = $inventoryCounts->sum(function($count) {
            return $count->items->where('difference', '!=', 0)->count();
        });

        return [
            'total_counts' => $inventoryCounts->count(),
            'total_items_checked' => $totalItems,
            'total_discrepancies' => $totalDiscrepancies,
            'accuracy_rate' => $totalItems > 0 ? (($totalItems - $totalDiscrepancies) / $totalItems) * 100 : 0,
            'total_value_loss' => $inventoryCounts->sum(function($count) {
                return $count->items->sum(function($item) {
                    return abs($item->difference) * $item->medicine->supplier_price;
                });
            })
        ];
    }

    private function analyzeDiscrepancies($inventoryCounts)
    {
        $allItems = collect();
        foreach ($inventoryCounts as $count) {
            $allItems = $allItems->merge($count->items);
        }

        return [
            'critical_discrepancies' => $allItems->where('difference', '<', -10)->count(),
            'moderate_discrepancies' => $allItems->whereBetween('difference', [-10, -5])->count(),
            'minor_discrepancies' => $allItems->whereBetween('difference', [-5, 0])->count(),
            'overstock_items' => $allItems->where('difference', '>', 0)->count(),
            'most_affected_categories' => $allItems->where('difference', '!=', 0)
                ->groupBy('medicine.category.name')
                ->map(function($items) {
                    return $items->count();
                })
                ->sortDesc()
                ->take(5)
        ];
    }

    private function categoryDiscrepancyAnalysis($inventoryCounts)
    {
        $categoryData = [];
        foreach ($inventoryCounts as $count) {
            foreach ($count->items as $item) {
                $categoryName = $item->medicine->category->name;
                if (!isset($categoryData[$categoryName])) {
                    $categoryData[$categoryName] = [
                        'total_items' => 0,
                        'discrepancies' => 0,
                        'total_value_loss' => 0
                    ];
                }

                $categoryData[$categoryName]['total_items']++;
                if ($item->difference != 0) {
                    $categoryData[$categoryName]['discrepancies']++;
                    $categoryData[$categoryName]['total_value_loss'] +=
                        abs($item->difference) * $item->medicine->supplier_price;
                }
            }
        }

        return $categoryData;
    }

    private function generateTrends($inventoryCounts)
    {
        return [
            'monthly_trends' => $inventoryCounts->groupBy(function($count) {
                return Carbon::parse($count->count_date)->format('Y-m');
            })->map(function($counts) {
                return [
                    'counts' => $counts->count(),
                    'total_items' => $counts->sum(function($count) {
                        return $count->items->count();
                    }),
                    'discrepancies' => $counts->sum(function($count) {
                        return $count->items->where('difference', '!=', 0)->count();
                    })
                ];
            })
        ];
    }

    private function generateRecommendations($inventoryCounts)
    {
        $totalDiscrepancies = $inventoryCounts->sum(function($count) {
            return $count->items->where('difference', '!=', 0)->count();
        });


        $recommendations = [];

        if ($totalDiscrepancies > 50) {
            $recommendations[] = 'زيادة وتيرة عمليات الجرد';
        }

        if ($inventoryCounts->avg(function($count) {
            return $count->items->where('difference', '<', 0)->count();
        }) > 10) {
            $recommendations[] = 'تحسين إجراءات الأمان للمخزون';
        }

        if ($inventoryCounts->count() < 12) {
            $recommendations[] = 'إجراء جرد شهري منتظم';
        }

        return $recommendations;
    }
}
