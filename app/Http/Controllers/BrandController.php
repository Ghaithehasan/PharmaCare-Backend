<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Medicine; // Added this import for the new_brand_id check

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();
        return response()->json([
            'status' => true,
            'data' => $brands
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'company_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Brand created successfully',
            'data' => $brand
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $brand
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'company_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully',
            'data' => $brand
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            // التحقق من وجود أدوية في العلامة التجارية
            $medicinesCount = Medicine::where('brand_id', $id)->count();

            if ($medicinesCount > 0) {
                // إذا كان هناك أدوية في العلامة التجارية، نحتاج لنقلها أولاً
                if (!$request->has('new_brand_id')) {
                    // جلب جميع العلامات التجارية الأخرى المتاحة
                    $availableBrands = Brand::where('id', '!=', $id)->get();

                    return response()->json([
                        'status' => false,
                        'status_code' => 400,
                        'message' => 'لا يمكن حذف العلامة التجارية لوجود أدوية فيها',
                        'requires_transfer' => true,
                        'medicines_count' => $medicinesCount,
                        'brand_name' => $brand->name,
                        'available_brands' => $availableBrands->map(function($brand) {
                            return [
                                'id' => $brand->id,
                                'name' => $brand->name
                            ];
                        }),
                        'message_details' => "يوجد {$medicinesCount} دواء في العلامة التجارية '{$brand->name}'. يرجى اختيار علامة تجارية أخرى لنقل الأدوية إليها قبل حذف العلامة التجارية."
                    ], 400);
                }

                // نقل الأدوية إلى العلامة التجارية الجديدة
                $newBrandId = $request->new_brand_id;
                $newBrand = Brand::find($newBrandId);

                if (!$newBrand) {
                    return response()->json([
                        'status' => false,
                        'status_code' => 404,
                        'message' => 'العلامة التجارية المحددة غير موجودة',
                        'errors' => [
                            'new_brand_id' => ['العلامة التجارية المحددة غير موجودة']
                        ]
                    ], 404);
                }

                // نقل جميع الأدوية إلى العلامة التجارية الجديدة
                $transferredMedicines = Medicine::where('brand_id', $id)->update([
                    'brand_id' => $newBrandId
                ]);

                // حذف العلامة التجارية
                $brand->delete();

                return response()->json([
                    'status' => true,
                    'status_code' => 200,
                    'message' => 'تم حذف العلامة التجارية بنجاح',
                    'data' => [
                        'deleted_brand' => [
                            'id' => $id,
                            'name' => $brand->name
                        ],
                        'transfer_details' => [
                            'medicines_transferred' => $transferredMedicines,
                            'new_brand' => [
                                'id' => $newBrand->id,
                                'name' => $newBrand->name
                            ]
                        ]
                    ]
                ]);

            } else {
                // لا توجد أدوية في العلامة التجارية، يمكن حذفها مباشرة
                $brand->delete();

                return response()->json([
                    'status' => true,
                    'status_code' => 200,
                    'message' => 'تم حذف العلامة التجارية بنجاح',
                    'data' => [
                        'deleted_brand' => [
                            'id' => $id,
                            'name' => $brand->name
                        ],
                        'transfer_details' => null
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'حدث خطأ أثناء حذف العلامة التجارية',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
