<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Category;

use App\Models\MedicineAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicineController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'medicine_name' => 'required|string|unique:medicines,medicine_name|max:255',
            'sentific_name' => 'nullable|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'bar_code' => 'required|string|unique:medicines,bar_code|max:50',
            'type' => 'required|in:package,unit',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'alert_quantity' => 'nullable|integer|min:1',
            'people_price' => 'required|numeric|min:0',
            'supplier_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Create the medicine
        $medicine = Medicine::create($validatedData);

        // Handle attachment if exists
        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment');
            
            // Generate unique filename
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            
            // Store file in storage/app/public/medicine-attachments
            $filePath = $file->storeAs('medicine-attachments', $fileName, 'public');

            // Create attachment record
            MedicineAttachment::create([
                'medicine_id' => $medicine->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath
            ]);
        }

        $medicine->load('attachments');

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'medicine' => $medicine,
            'message' => 'تم إضافة الدواء والمرفق بنجاح'
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
                'status_code'=>404
            ],404);
        }
        $medicine->delete();
        return response()->json([
            'status' => true,
            'status_code' => 200,
            // 'message' => __('messages.medicine_deleted'),
        ], 200);
    }


    public function storeAlternative(Request $request, $medicineId)
    {
        $medicine = Medicine::find($medicineId);
        $arr_of_alternatives = $request->alternative_ids;
        
        // الحصول على كائنات Medicine بدلاً من المصفوفة
        $alternatives = Medicine::whereIn('id', $arr_of_alternatives)->get();
        
        $medicine->addAlternative($alternatives);
    
        return response()->json(['message' => '✅ تم إضافة الدواء البديل بنجاح!','status_code'=>200,'status'=>true]);
    }


    public function showAllAlternatives($medicineId)
    {
        // Find the medicine
        $medicine = Medicine::find($medicineId);

        if (!$medicine) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'لم يتم العثور على الدواء',
                'errors' => ['medicine' => 'الدواء غير موجود في النظام']
            ], 404);
        }

        // Get alternatives using the model's method
        $alternatives = $medicine->alternatives()
            ->with('category')
            ->select('id', 'medicine_name', 'sentific_name', 'arabic_name', 'bar_code', 'type', 'quantity', 'people_price', 'supplier_price', 'category_id')
            ->orderBy('medicine_name')
            ->get();

        // Prepare the response data
        $response = [
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الأدوية البديلة بنجاح',
            'data' => [
                'medicine' => [
                    'id' => $medicine->id,
                    'name' => $medicine->medicine_name,
                    'scientific_name' => $medicine->sentific_name,
                    'arabic_name' => $medicine->arabic_name,
                    'barcode' => $medicine->bar_code,
                ],
                'alternatives' => $alternatives->map(function($alternative) {
                    return [
                        'id' => $alternative->id,
                        'name' => $alternative->medicine_name,
                        'scientific_name' => $alternative->sentific_name,
                        'arabic_name' => $alternative->arabic_name,
                        'barcode' => $alternative->bar_code,
                        'type' => $alternative->type,
                        'quantity' => $alternative->quantity,
                        'prices' => [
                            'people_price' => $alternative->people_price,
                            'supplier_price' => $alternative->supplier_price,
                        ],
                        'category' => $alternative->category ? [
                            'id' => $alternative->category->id,
                            'name' => $alternative->category->name
                        ] : null,
                    ];
                }),
            ],
            'meta' => [
                'total_alternatives' => $alternatives->count()
            ]
        ];

        return response()->json($response, 200);
    }

    public function removeAlternative(Request $request, $medicineId)
    {
        $medicine = Medicine::find($medicineId);
        $arr_of_alternatives = $request->alternative_ids;
        
        // الحصول على كائنات Medicine
        $alternatives = Medicine::whereIn('id', $arr_of_alternatives)->get();
        
        $medicine->removeAlternative($alternatives);
    
        return response()->json(['message' => '✅ تم إزالة الأدوية البديلة بنجاح!','status'=>true,'status_code'=>200]);
    }
}


