<?php

namespace App\Http\Controllers;

use App\Models\MedicineForm;
use Illuminate\Http\Request;

class MedicineFormController extends Controller
{
    /**
     * عرض جميع الأشكال الدوائية
     */
    public function index()
    {
        $medicineForms = MedicineForm::select(['id', 'name', 'description'])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'تم جلب الأشكال الدوائية بنجاح',
            'data' => $medicineForms->map(function($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->name,
                    'description' => $form->description,
                    'medicines_count' => $form->medicines()->count()
                ];
            })
        ]);
    }

    /**
     * إضافة شكل دوائي جديد
     */
    public function store(Request $request)
    {
        // dd();
        $validatedData = $request->validate([
            'name' => 'required|string|unique:medicine_forms,name|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $medicineForm = MedicineForm::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الشكل الدوائي بنجاح',
            'status_code' => 200,
            'data' => $medicineForm
        ]);
    }

    /**
     * حذف شكل دوائي
     */
    public function destroy($id)
    {
        $medicineForm = MedicineForm::find($id);
        
        if (!$medicineForm) {
            return response()->json([
                'status' => false,
                'message' => 'الشكل الدوائي غير موجود',
                'status_code' => 404
            ], 404);
        }

        // التحقق من وجود أدوية مرتبطة بهذا الشكل
        if ($medicineForm->medicines()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن حذف الشكل الدوائي لوجود أدوية مرتبطة به',
                'status_code' => 400
            ], 400);
        }

        $medicineForm->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الشكل الدوائي بنجاح',
            'status_code' => 200
        ]);
    }
} 