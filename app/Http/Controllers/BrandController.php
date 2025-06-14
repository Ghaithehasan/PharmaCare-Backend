<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

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
    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }
}
