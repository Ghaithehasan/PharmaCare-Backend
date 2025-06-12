<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierNotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicineController;
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
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [SupplierNotificationController::class, 'show_all_notifications'])
                ->name('index');
            Route::post('/{id}/mark-as-read', [SupplierNotificationController::class, 'markAsRead'])
                ->name('mark-as-read');
            Route::post('/mark-all-as-read', [SupplierNotificationController::class, 'markAllAsRead'])
                ->name('mark-all-as-read');
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
    // return view('pusher_test');
});


// Route::get('/{page}', [AdminController::class, 'index'])
    // ->where('page', '.*');

/*
*/




// Route::get('users-show', [UserController::class, 'index'])->name('show_user');
// Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
// Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
