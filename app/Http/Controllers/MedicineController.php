<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\MedicineAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicineController extends Controller
{

    public function index(Request $request)
    {
        // التحقق من صحة المعلمات
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|in:package,unit',
            'quantity_filter' => 'nullable|in:low,out,available',
            'expiry_filter' => 'nullable|in:expired,expiring_soon,valid'
        ]);

        // بناء الاستعلام الأساسي مع علاقة order_items
        $query = Medicine::with(['category', 'medicineForm', 'orderItems'])
            ->select([
                'id',
                'medicine_name',
                'sentific_name',
                'arabic_name',
                'bar_code',
                'type',
                'quantity',
                'alert_quantity',
                'supplier_price',
                'people_price',
                'tax_rate',
                'category_id',
                'medicine_form_id'
            ]);



        // تطبيق الفلاتر
        $this->applyFilters($query, $validated);

        // تطبيق الترتيب الافتراضي
        $query->orderBy('medicine_name', 'asc');

        // تطبيق الصفحات
        $medicines = $query->paginate(15);

        // تنسيق البيانات
        $medicines->getCollection()->transform(function ($medicine) {
            return $this->formatMedicineData($medicine);
        });

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الأدوية بنجاح',
            'data' => $medicines->items(),
            'meta' => [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'total' => $medicines->total()
            ]
        ]);
    }

    /**
     * تطبيق الفلاتر على الاستعلام
     */
    private function applyFilters($query, array $filters)
    {
        // فلتر البحث
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('medicine_name', 'like', "%{$search}%")
                  ->orWhere('sentific_name', 'like', "%{$search}%")
                  ->orWhere('arabic_name', 'like', "%{$search}%")
                  ->orWhere('bar_code', 'like', $search);
            });
        }

        // فلتر التصنيف
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // فلتر النوع
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // فلتر الكمية
        if (!empty($filters['quantity_filter'])) {
            switch ($filters['quantity_filter']) {
                case 'low':
                    $query->where('quantity', '<=', DB::raw('alert_quantity'))->where('quantity','>',0);
                    break;
                case 'out':
                    $query->where('quantity', 0);
                    break;
                case 'available':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }

        // فلتر تاريخ الصلاحية - يستخدم order_items مع مراعاة الكمية الفعلية
        if (!empty($filters['expiry_filter'])) {
            $now = Carbon::now();
            switch ($filters['expiry_filter']){
                case 'expired':
                    $query->whereHas('batches', function($q) use ($now) {
                        $q->where('expiry_date', '<', $now)
                          ->where('quantity', '>', 0); // فقط الدفعات التي لا تزال في المخزون
                    })->where('quantity','>',0); // التأكد من وجود كمية فعلية
                    break;
                case 'expiring_soon':
                    $query->whereHas('batches', function($q) use ($now) {
                        $q->where('expiry_date', '>', $now)
                          ->where('expiry_date', '<=', $now->copy()->addDays(30))
                          ->where('quantity', '>', 0); // فقط الدفعات التي لا تزال في المخزون
                    })->where('quantity','>',0); // التأكد من وجود كمية فعلية
                    break;
                case 'valid':
                    $query->whereHas('batches', function($q) use ($now) {
                        $q->where('expiry_date', '>', $now)
                          ->where('quantity', '>', 0); // فقط الدفعات التي لا تزال في المخزون
                    })->where('quantity','>',0); // التأكد من وجود كمية فعلية
                    break;
            }
        }
    }

    /**
     * تنسيق بيانات الدواء
     */
    private function formatMedicineData($medicine)
    {
        // حساب معلومات تاريخ الانتهاء من order_items
        $expiryInfo = $this->calculateExpiryInfo($medicine);

        return [
            'id' => $medicine->id,
            'name' => $medicine->medicine_name,
            'scientific_name' => $medicine->sentific_name,
            'arabic_name' => $medicine->arabic_name,
            'barcode' => $medicine->bar_code,
            'type' => $medicine->type,
            'quantity' => $medicine->quantity,
            'alert_quantity' => $medicine->alert_quantity,
            'prices' => [
                'supplier_price' => $medicine->supplier_price,
                'people_price' => $medicine->people_price,
                'tax_rate' => $medicine->tax_rate,
            ],
            'category' => $medicine->category ? [
                'id' => $medicine->category->id,
                'name' => $medicine->category->name
            ] : null,
            'medicine_form' => $medicine->medicineForm ? [
                'id' => $medicine->medicineForm->id,
                'name' => $medicine->medicineForm->name,
                'description' => $medicine->medicineForm->description
            ] : null,
            'expiry_info' => $expiryInfo,
            'status' => [
                'is_low' => $medicine->quantity <= $medicine->alert_quantity,
                'is_out' => $medicine->quantity == 0,
                'is_expired' => $expiryInfo['has_expired'],
                'is_expiring_soon' => $expiryInfo['has_expiring_soon']
            ]
        ];
    }

    /**
     * حساب معلومات تاريخ الانتهاء من order_items
     */
    private function calculateExpiryInfo($medicine)
{
    $now = Carbon::now();
    $batches = $medicine->batches()->where('quantity', '>', 0)->get();

    $hasExpired = false;
    $hasExpiringSoon = false;
    $earliestExpiry = null;

    foreach ($batches as $batch) {
        if ($batch->expiry_date) {
            if ($batch->expiry_date < $now) {
                $hasExpired = true;
            } elseif ($batch->expiry_date <= $now->copy()->addDays(30)) {
                $hasExpiringSoon = true;
            }

            if (!$earliestExpiry || $batch->expiry_date < $earliestExpiry) {
                $earliestExpiry = $batch->expiry_date;
            }
        }
    }


    return [
        'has_expired' => $hasExpired,
        'has_expiring_soon' => $hasExpiringSoon,
        'earliest_expiry_date' => $earliestExpiry,
    ];
}

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'medicine_name' => 'required|string|unique:medicines,medicine_name|max:255',
            'sentific_name' => 'nullable|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'bar_code' => 'required|string|unique:medicines,bar_code|max:50',
            'type' => 'required|in:package,unit',
            'category_id' => 'required|exists:categories,id',
            'medicine_form_id' => 'required|exists:medicine_forms,id',
            'brand_id' => 'required|exists:brands,id',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'nullable|integer|min:1',
            'people_price' => 'required|numeric|min:0',
            // 'expiry_date' => 'required|date',
            'supplier_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Create the medicine
        $medicine = Medicine::create($validatedData);

        $attachments = [];
        // Handle attachments if exists
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Generate unique filename
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

                // Store file in storage/app/public/medicine-attachments
                $filePath = $file->storeAs('medicine-attachments', $fileName, 'public');

                // Create attachment record
                $attachment = MedicineAttachment::create([
                    'medicine_id' => $medicine->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath
                ]);

                // إضافة المسار الكامل للمرفق
                $attachments[] = [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'full_url' => asset('storage/' . $attachment->file_path)
                ];
            }
        }


        return response()->json([
            'status' => true,
            'status_code' => 200,
            'medicine' => $this->formatMedicineData($medicine),
            'attachments' => $attachments,
            'message' => 'تم إضافة الدواء والمرفقات بنجاح'
        ], 200);
    }


    public function storeCategory(Request $request)
    {
        // dd();
        $validatedData = $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category = Category::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الفئة بنجاح',
            'status_code' => 200,
            'category' => $category,
        ]);
    }

    public function generateNumericBarcode()
    {
        $min = 10000000; // أصغر رقم مكون من 8 أرقام
        $max = 99999999; // أكبر رقم مكون من 8 أرقام

        do {
            $barcode = mt_rand($min, $max);
        } while (Medicine::where('bar_code', $barcode)->exists()); // التأكد من عدم التكرار داخل قاعدة البيانات

        return response()->json(['bar_code' => $barcode , 'status' => true , 'status_code' => 200]);
    }

    public function destroy($id)
    {
        $medicine = Medicine::find($id);
        if(!$medicine)
        {
            return response()->json([
                'status' => false,
                'message' => 'medecine not found !',
                'status_code'=>404
            ],404);
        }
        $alternatives=$medicine->alternatives()->get();
        $itemRemoved = $medicine->id;

        foreach($alternatives as $alt)
        {
            $alt->alternative_ids = array_filter($alt->alternative_ids , fn($item) => $item !== $itemRemoved);
            $alt->alternative_ids = array_values($alt->alternative_ids);
            $alt->save();

        }

        $medicine->delete();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => __('messages.medicine_deleted'),
        ], 200);
    }


    public function storeAlternative(Request $request, $medicineId)
    {
        $request->validate([
            'alternative_ids' => 'required|array',
            'alternative_ids.*' => 'exists:medicines,id',
            'is_bidirectional' => 'boolean' // إضافة خيار للعلاقة المتبادلة
        ]);

        $medicine = Medicine::findOrFail($medicineId);
        $alternatives = Medicine::whereIn('id', $request->alternative_ids)->get();

        if ($request->is_bidirectional) {
            $medicine->addBidirectionalAlternative($alternatives);
            $message = '✅ تم إضافة البدائل المتبادلة بنجاح!';
        } else {
            $medicine->addAlternative($alternatives);
            $message = '✅ تم إضافة البدائل بنجاح!';
        }

        return response()->json([
            'message' => $message,
            'status_code' => 200,
            'status' => true
        ]);
    }


    public function showAllAlternatives($medicineId)
    {
        // dd();
        // البحث عن الدواء
        $medicine = Medicine::find($medicineId);

        if (!$medicine) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => '⚠️ لم يتم العثور على الدواء!',
                'errors' => ['medicine' => '❌ هذا الدواء غير موجود في النظام']
            ], 404);
        }

        // جلب البدائل
        $alternatives = $medicine->alternatives()
            ->with('category')
            ->orderBy('medicine_name')
            ->get(['id', 'medicine_name', 'sentific_name', 'bar_code', 'type', 'quantity', 'people_price', 'supplier_price', 'category_id']);

        // إذا لم يكن هناك أي بديل، إظهار رسالة جميلة
        if ($alternatives->isEmpty()) {
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => '✅ هذا الدواء لا يحتوي على أي بدائل متاحة حاليًا!',
                'data' => [
                    'medicine' => [
                        'id' => $medicine->id,
                        'name' => $medicine->medicine_name,
                        'scientific_name' => $medicine->sentific_name,
                        'barcode' => $medicine->bar_code,
                    ],
                    'alternatives' => []
                ],
                'meta' => [
                    'total_alternatives' => 0
                ]
            ], 200);
        }

        // إعداد بيانات الاستجابة
        $response = [
            'status' => true,
            'status_code' => 200,
            'message' => '✅ تم جلب الأدوية البديلة بنجاح!',
            'data' => [
                'medicine' => [
                    'id' => $medicine->id,
                    'scientific_name' => $medicine->sentific_name,
                    'barcode' => $medicine->bar_code,
                ],
                'alternatives' => $alternatives->map(fn ($alternative) => [
                    'id' => $alternative->id,
                    'name' => $alternative->medicine_name,
                    'scientific_name' => $alternative->sentific_name,
                    'barcode' => $alternative->bar_code,
                    'type' => $alternative->type,
                    'quantity' => $alternative->quantity,
                    'prices' => [
                        'people_price' => $alternative->people_price,
                        'supplier_price' => $alternative->supplier_price,
                    ],
                    'category' => optional($alternative->category)->only(['name','id']),
                ]),
            ],
            'meta' => [
                'total_alternatives' => $alternatives->count()
            ]
        ];

        return response()->json($response, 200);
    }

    /**
     * عرض دواء واحد
     */
    public function show($id)
    {
        $medicine = Medicine::with(['category', 'attachments'])
            ->find($id);

        if (!$medicine) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'لم يتم العثور على الدواء',
                'errors' => ['medicine' => 'الدواء غير موجود في النظام']
            ], 404);
        }

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب بيانات الدواء بنجاح',
            'data' => $this->formatMedicineData($medicine)
        ]);
    }

    /**
     * عرض جميع الأقسام
     */
    public function showCategories()
    {
        $categories = Category::select(['id', 'name', 'description'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الأقسام بنجاح',
            'data' => $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'medicines_count' => $category->medicines()->count()
                ];
            })
        ]);
    }

    /**
     * عرض الأدوية منخفضة الكمية
     */
    public function getLowQuantityMedicines()
    {
        $medicines = Medicine::with(['category'])
            ->where('quantity', '<=', DB::raw('alert_quantity'))
            ->orderBy('quantity', 'asc')
            ->get();

        $medicines->transform(function ($medicine) {
            return $this->formatMedicineData($medicine);
        });

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الأدوية منخفضة الكمية بنجاح',
            'data' => $medicines
        ]);
    }

    /**
     * تحديث كمية الدواء
     */
    public function updateQuantity(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:add,subtract,set'
        ]);

        $medicine = Medicine::findOrFail($id);

        \Log::info('Starting quantity update', [
            'medicine_id' => $medicine->id,
            'current_quantity' => $medicine->quantity,
            'operation' => $validated['operation'],
            'new_quantity' => $validated['quantity']
        ]);

        switch ($validated['operation']) {
            case 'add':
                $medicine->quantity += $validated['quantity'];
                break;
            case 'subtract':
                if ($medicine->quantity < $validated['quantity']) {
                    return response()->json([
                        'status' => false,
                        'status_code' => 400,
                        'message' => 'الكمية المطلوبة غير متوفرة'
                    ], 400);
                }
                $medicine->quantity -= $validated['quantity'];
                break;
            case 'set':
                $medicine->quantity = $validated['quantity'];
                break;
        }

        $medicine->save();


        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم تحديث الكمية بنجاح',
            'data' => $this->formatMedicineData($medicine)
        ]);
    }



    public function generate_barcode($medicine_id)
    {
        $quantity=request()->quantity;
        // dd($quantity);
        $medicine = Medicine::findOrFail($medicine_id);
        $barcode = base64_encode((new BarcodeGeneratorPNG())->getBarcode($medicine->bar_code, BarcodeGeneratorPNG::TYPE_CODE_128));

        $pdf = Pdf::loadView('barcode', compact('medicine', 'barcode', 'quantity'))
                  ->setPaper('A4', 'portrait')
                  ->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);

        return response($pdf->output(), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'inline; filename="medicine_labels.pdf"');
    }

}


