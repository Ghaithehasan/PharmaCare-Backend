<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierNotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\InvoicesController;
use Illuminate\Support\Facades\Broadcast;
use App\Events\Pusher;
use App\Http\Middleware\SupplierAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Public Routes
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::post('/login', [SupplierController::class, 'login'])->name('login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/suppliers/dashboard', [SupplierController::class, 'show_supplier_dashboard'])
        ->name('dashboard_page');

    // Profile Management
    Route::prefix('supplier')->name('supplier.')->group(function () {
        // Profile Routes
        Route::get('/profile', [SupplierController::class, 'show_supplier_profile'])
            ->name('profile');
        Route::post('/update/profile', [SupplierController::class, 'update'])
            ->name('update.profile');
        
        // Account Settings
        Route::get('/account', [SupplierController::class, 'show_account'])
            ->name('account.settings');
        Route::delete('/delete/account', [SupplierController::class, 'delete_account'])
            ->name('delete.account');
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {//supplier.notifications.show
            Route::get('/', [SupplierNotificationController::class, 'show_all_notifications'])
                ->name('index');
            Route::post('/{id}/mark-as-read', [SupplierNotificationController::class, 'markAsRead'])
                ->name('mark-as-read');
            Route::post('/mark-all-as-read', [SupplierNotificationController::class, 'markAllAsRead'])
                ->name('mark-all-as-read');
            Route::get('/{id}' , [SupplierNotificationController::class, 'show'])->name('show');
        });

        Route::prefix('orders')->name('orders.')->group(function(){
            Route::get('new-orders',[SupplierOrderController::class,'index'])->name('index');
            Route::post('cancelled-order',[SupplierOrderController::class,'cancelled'])->name('cancelled');
            Route::get('print-order/{id}',[ SupplierOrderController::class, 'PrintOrder']);
            Route::post('accept-order',[SupplierOrderController::class, 'AcceptOrder'])->name('accept');
            Route::get('accepted-orders',[SupplierOrderController::class,'accepted'])->name('accepted');
            Route::post('update-order',[SupplierOrderController::class, 'updateOrder'])->name('update');
            Route::get('show-orders-cancelled',[SupplierOrderController::class, 'show_cancel_orders'])->name('show_canceled_orders');
            Route::get('show-all-orders',[SupplierOrderController::class, 'show_All_orders'])->name('show_all_order');
            Route::get('exports_orders',[ SupplierOrderController::class, 'ExportOrder'])->name('exports_orders');
            Route::get('completed-order',[ SupplierOrderController::class, 'show_completed_orders'])->name('show_completed_order');
            Route::post('/supplier/orders/update-expiry/{item}', [SupplierOrderController::class, 'updateExpiry'])->name('update_expiry');
        
        });

        // Invoices Routes
        Route::prefix('invoices')->name('invoices.')->group(function(){
            Route::get('/', [InvoicesController::class, 'index'])->name('index');
            Route::get('show/{id}', [InvoicesController::class, 'show'])->name('show');
            Route::get('show-pdf/{id}', [InvoicesController::class, 'show_pdf_invoice'])->name('show-pdf');
            // Route::get('download/{id}', [InvoicesController::class, 'download'])->name('download');
            Route::post('update-status/{id}', [InvoicesController::class, 'updateStatus'])->name('update-status');
            Route::get('unpaid', [InvoicesController::class, 'show_un_paid_invoices'])->name('unpaid');
            Route::get('paid', [InvoicesController::class, 'show_paid_invoices'])->name('paid');
            Route::get('partially', [InvoicesController::class, 'partially'])->name('partially');
            Route::post('add-to-archive/{id}',[InvoicesController::class, 'add_to_archive'])->name('archive');
            Route::get('show-archive-invoices',[InvoicesController::class, 'show_archive_invoice'])->name('show_archive');
        });

        Route::prefix('payments')->name('payments.')->group(function(){
            Route::get('show-all-payments',[PaymentController::class,'show_all_payments'])->name('all_payments');
            Route::get('show-all-pending-payments',[PaymentController::class,'show_all_pending_payments'])->name('pending_payments');
            Route::get('show-all-confirmed-payments',[PaymentController::class,'show_all_confirmed_payments'])->name('confirmed_payments');
            Route::get('show-all-rejected-payments',[PaymentController::class,'show_all_rejected_payments'])->name('rejected_payments');
            
            Route::post('chang-status/{id}',[PaymentController::class,'change_payment_status'])->name('update_status');
        });







    });

    // Logout
    Route::post('/logout', [SupplierController::class, 'logout'])
        ->name('logout');
});


Route::get('/ddddddd', function () {
    // $data = [
        // 'message' => 'Hello',
        // 'time' => now()->toDateTimeString(),
    // ];
    // broadcast(new Pusher($data));
    $supplier = auth()->user();
    return view('chart-chartjs',compact('supplier'));
});




Route::get('Accept-the-order/{id}',[SupplierOrderController::class, 'ShowPageComplete'])->name('coniferm');
Route::post('complete-the-order/{id}',[SupplierOrderController::class, 'completeOrder'])->name('complete');



// Route::get('users-show', [UserController::class, 'index'])->name('show_user');
// Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
// Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/invoices/{id}/download', [InvoicesController::class, 'download'])->name('invoices.download');
