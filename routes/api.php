<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DamagedMedicineController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\MedicineFormController;
use App\Http\Middleware\ApiLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login-user' , [AuthController::class , 'login'])->middleware([ApiLocalization::class]);

Route::post('register-user' , [AuthController::class , 'register'])->middleware([ApiLocalization::class]);


Route::post('logout-user',[AuthController::class , 'logout'])->middleware(['auth:api' , ApiLocalization::class]);

Route::post('verify-email-code' , [AuthController::class , 'verifyEmail'])->middleware(['auth:api' , ApiLocalization::class]);

Route::get('show-profile',[AuthController::class,'show_profile_details'])->middleware(['auth:api' , ApiLocalization::class]);

Route::put('update-profile', [AuthController::class, 'updateProfile'])->middleware(['auth:api' ,  ApiLocalization::class]);

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

Route::apiResource('brands' , BrandController::class)->middleware([ApiLocalization::class]);

Route::apiResource('orders',OrderController::class)->middleware([ApiLocalization::class]);

Route::apiResource('payments',PaymentController::class)->middleware([ApiLocalization::class]);

// Route::get('medecines-with-low-quantity')

Route::post('add-category', [MedicineController::class, 'storeCategory'])->middleware([ApiLocalization::class]);

Route::get('generaite-barcode', [MedicineController::class, 'generateNumericBarcode'])->middleware([ApiLocalization::class]);


Route::get('show-supplier-details/{id}',[SupplierController::class , 'ShowSupplierDetails'])->middleware([ApiLocalization::class]);

Route::get('show-all-permissions' , [RoleController::class , 'getAllPermissions'])->middleware([ApiLocalization::class]);

Route::get('bar-code/{id}' , [MedicineController::class , 'generate_barcode']);


// مسارات الأشكال الدوائية
Route::get('medicine-forms', [MedicineFormController::class, 'index'])->middleware([ApiLocalization::class]);
Route::post('medicine-forms', [MedicineFormController::class, 'store'])->middleware([ApiLocalization::class]);
Route::delete('medicine-forms/{id}', [MedicineFormController::class, 'destroy'])->middleware([ApiLocalization::class]);

Route::post('create-new-invoice',[InvoicesController::class,'store'])->middleware([ApiLocalization::class]);
Route::get('show-all-invoices',[InvoicesController::class,'show_all_invoice_with_filter'])->middleware([ApiLocalization::class]);
Route::get('show-paid-invoices',[InvoicesController::class,'show_paid_invoices_api'])->middleware([ApiLocalization::class]);
Route::get('show-unpaid-invoices',[InvoicesController::class,'show_unpaid_invoices_api'])->middleware([ApiLocalization::class]);
Route::get('show-partially-invoices',[InvoicesController::class,'show_partially_paid_invoices_api'])->middleware([ApiLocalization::class]);
Route::get('invoices/{id}/download-pdf', [InvoicesController::class, 'download_invoice_pdf_api'])->middleware([ApiLocalization::class]);
Route::get('invoices/{id}/view-pdf', [InvoicesController::class, 'view_invoice_pdf_api'])->middleware([ApiLocalization::class]);
Route::get('show-invoice-details/{id}', [InvoicesController::class, 'show_invoice_with_payments_api'])->middleware([ApiLocalization::class]);

// تقارير جرد المخزون
Route::prefix('reports')->group(function () {
    Route::get('/comprehensive-inventory', [ReportController::class, 'comprehensiveInventoryReport'])->middleware([ApiLocalization::class]);
    Route::get('/Medicines-Expiry-date',[ReportController::class, 'ExpiryReports'])->middleware([ApiLocalization::class]);
    Route::get('/discrepancy', [ReportController::class, 'discrepancyReport'])->middleware([ApiLocalization::class]);
    Route::get('/missing-leakage', [ReportController::class, 'missingAndLeakageReport'])->middleware([ApiLocalization::class]);
    Route::get('/time-performance', [ReportController::class, 'timePerformanceReport'])->middleware([ApiLocalization::class]);
    Route::get('/category-analysis', [ReportController::class, 'categoryAnalysisReport'])->middleware([ApiLocalization::class]);
    Route::get('/predictive-analysis', [ReportController::class, 'predictiveAnalysisReport'])->middleware([ApiLocalization::class]);
    Route::get('/talif-reports',[ReportController::class, 'talif_report'])->middleware([ApiLocalization::class]);
    Route::post('/generate-pdf', [ReportController::class, 'generatePDFReport'])->middleware([ApiLocalization::class]);

});




