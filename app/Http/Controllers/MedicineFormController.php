<?php

namespace App\Http\Controllers;

use App\Models\MedicineForm;
use Illuminate\Http\Request;
use App\Models\Medicine; // Added this import for the new_medicine_form_id check

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
    public function destroy(Request $request, $id)
    {
        try {
            $medicineForm = MedicineForm::findOrFail($id);

            // التحقق من وجود أدوية في الشكل الدوائي
            $medicinesCount = Medicine::where('medicine_form_id', $id)->count();

            if ($medicinesCount > 0) {
                // إذا كان هناك أدوية في الشكل الدوائي، نحتاج لنقلها أولاً
                if (!$request->has('new_medicine_form_id')) {
                    // جلب جميع الأشكال الدوائية الأخرى المتاحة
                    $availableMedicineForms = MedicineForm::where('id', '!=', $id)->get();

                    return response()->json([
                        'status' => false,
                        'status_code' => 400,
                        'message' => 'لا يمكن حذف الشكل الدوائي لوجود أدوية مرتبطة به',
                        'requires_transfer' => true,
                        'medicines_count' => $medicinesCount,
                        'medicine_form_name' => $medicineForm->name,
                        'available_medicine_forms' => $availableMedicineForms->map(function($form) {
                            return [
                                'id' => $form->id,
                                'name' => $form->name,
                                'description' => $form->description
                            ];
                        }),
                        'message_details' => "يوجد {$medicinesCount} دواء في الشكل الدوائي '{$medicineForm->name}'. يرجى اختيار شكل دوائي آخر لنقل الأدوية إليه قبل حذف الشكل الدوائي."
                    ], 400);
                }

                // نقل الأدوية إلى الشكل الدوائي الجديد
                $newMedicineFormId = $request->new_medicine_form_id;
                $newMedicineForm = MedicineForm::find($newMedicineFormId);

                if (!$newMedicineForm) {
                    return response()->json([
                        'status' => false,
                        'status_code' => 404,
                        'message' => 'الشكل الدوائي المحدد غير موجود',
                        'errors' => [
                            'new_medicine_form_id' => ['الشكل الدوائي المحدد غير موجود']
                        ]
                    ], 404);
                }

                // نقل جميع الأدوية إلى الشكل الدوائي الجديد
                $transferredMedicines = Medicine::where('medicine_form_id', $id)->update([
                    'medicine_form_id' => $newMedicineFormId
                ]);

                // حذف الشكل الدوائي
                $medicineForm->delete();

                return response()->json([
                    'status' => true,
                    'status_code' => 200,
                    'message' => 'تم حذف الشكل الدوائي بنجاح',
                    'data' => [
                        'deleted_medicine_form' => [
                            'id' => $id,
                            'name' => $medicineForm->name
                        ],
                        'transfer_details' => [
                            'medicines_transferred' => $transferredMedicines,
                            'new_medicine_form' => [
                                'id' => $newMedicineForm->id,
                                'name' => $newMedicineForm->name
                            ]
                        ]
                    ]
                ]);

            } else {
                // لا توجد أدوية في الشكل الدوائي، يمكن حذفه مباشرة
                $medicineForm->delete();

                return response()->json([
                    'status' => true,
                    'status_code' => 200,
                    'message' => 'تم حذف الشكل الدوائي بنجاح',
                    'data' => [
                        'deleted_medicine_form' => [
                            'id' => $id,
                            'name' => $medicineForm->name
                        ],
                        'transfer_details' => null
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء حذف الشكل الدوائي',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}