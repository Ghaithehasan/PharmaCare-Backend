<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\DamagedMedicineController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\MedicineFormController;
use App\Http\Middleware\ApiLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login-user' , [AuthController::class , 'login'])->middleware([ApiLocalization::class]);

Route::post('register-user' , [AuthController::class , 'register'])->middleware([ApiLocalization::class]);

Route::post('logout-user',[AuthController::class , 'logout'])->middleware(['auth:api' , ApiLocalization::class]);

Route::post('verify-email-code' , [AuthController::class , 'verifyEmail'])->middleware(['auth:api' , ApiLocalization::class]);

Route::get('/verify-email', [AuthController::class, 'verifyEmailLink']);

Route::get('auth/google' , [AuthController::class , 'redirectToGoogle']);

Route::get('auth/google/callback' , [AuthController::class , 'handleGoogleCallback']);

Route::apiResource('users' , UserController::class)->middleware([ApiLocalization::class]);

Route::apiResource('roles' , RoleController::class)->middleware([ApiLocalization::class]);

Route::apiResource('suppliers' , SupplierController::class)->middleware([ApiLocalization::class]);

Route::post('add-alternative-medicines/{medicineId}' , [MedicineController::class, 'storeAlternative'])->middleware([ApiLocalization::class]);

// Route::post('delete-alternative-medicines/{medicineId}' , [MedicineController::class, 'removeAlternative'])->middleware([ApiLocalization::class]);

Route::apiResource('inventory-counts' , InventoryCountController::class)->middleware([ApiLocalization::class]);

Route::get('show-all-alternatives/{medicineId}',[MedicineController::class , 'showAllAlternatives'])->middleware([ApiLocalization::class]);

Route::get('show-damaged-medicine', [DamagedMedicineController::class, 'searchByBarcode'])->middleware([ApiLocalization::class]);

Route::post('add-damaged-medicine', [DamagedMedicineController::class, 'store'])->middleware([ApiLocalization::class]);

Route::get('show-all-damaged-medicines' , [DamagedMedicineController::class, 'index'])->middleware([ApiLocalization::class]);

Route::get('medicines/categories', [MedicineController::class, 'showCategories'])->middleware([ApiLocalization::class]);

Route::get('medicines/low-quantity', [MedicineController::class, 'getLowQuantityMedicines'])->middleware([ApiLocalization::class]);

Route::post('medicines/{id}/update-quantity', [MedicineController::class, 'updateQuantity'])->middleware([ApiLocalization::class]);

Route::apiResource('medicines' , MedicineController::class)->middleware([ApiLocalization::class]);

// Route::get('medecines-with-low-quantity')

Route::post('add-category', [MedicineController::class, 'storeCategory'])->middleware([ApiLocalization::class]);

Route::get('generaite-barcode', [MedicineController::class, 'generateNumericBarcode'])->middleware([ApiLocalization::class]);


Route::get('show-supplier-details/{id}',[SupplierController::class , 'ShowSupplierDetails'])->middleware([ApiLocalization::class]);

Route::get('show-all-permissions' , [RoleController::class , 'getAllPermissions'])->middleware([ApiLocalization::class]);

// مسارات الأشكال الدوائية
Route::get('medicine-forms', [MedicineFormController::class, 'index'])->middleware([ApiLocalization::class]);
Route::post('medicine-forms', [MedicineFormController::class, 'store'])->middleware([ApiLocalization::class]);
Route::delete('medicine-forms/{id}', [MedicineFormController::class, 'destroy'])->middleware([ApiLocalization::class]);

