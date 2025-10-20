<?php

// routes/web.php 

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CostumeController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\RenterStoreController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AdminController;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');
Route::view('/store/{user_id}', 'store.public_store')->name('public.store');

Route::middleware('auth')->group(function () {
    // --- STANDARD BREEZE PROFILE ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/renter/store', [ProfileController::class, 'updateRenterStore'])->name('renter.store.update');

    // --- MAIN REDIRECT LOGIC (Dashboard) ---
    Route::get('/dashboard', function () {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->hasRole('renter')) {
            return redirect()->route('renter.dashboard');
        }
        return redirect()->route('catalog');
    })->name('dashboard');

    // --- ADMIN ROUTES (Protected) ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // ANALYTICS & DASHBOARD
        Route::get('/home', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/renters/{user_id}/analytics', [AdminController::class, 'viewRenterAnalytics'])->name('renters.analytics');

        // MANAGEMENT (CRUD for Platform entities)
        Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
        Route::get('/costumes/all', [AdminController::class, 'manageCostumes'])->name('costumes.manage');
        Route::get('/costumes/approval', [AdminController::class, 'approvalQueue'])->name('costumes.approval');

        // DISCOUNT MANAGEMENT (NEW)
        Route::get('/discounts', [AdminController::class, 'manageDiscounts'])->name('discounts.manage');
        Route::post('/discounts/global', [AdminController::class, 'setGlobalDiscount'])->name('discounts.global.set');
        Route::get('/transactions', [AdminController::class, 'monitorTransactions'])->name('transactions');

        // BANNER MANAGEMENT (NEW)
        Route::get('/banners', [AdminController::class, 'manageBanners'])->name('banners.manage');
        Route::post('/banners/{id}', [AdminController::class, 'updateBanner'])->name('banners.update');

        Route::get('/stores', [AdminController::class, 'manageRenterStores'])->name('stores.manage');
        Route::get('/stores/{user_id}/view', [AdminController::class, 'viewRenterStoreDetails'])->name('stores.view');

        Route::get('/users/{user_id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::patch('/users/{user_id}/update', [AdminController::class, 'updateUser'])->name('users.update');
    });

    // --- RENTER ROUTES (Protected) ---
    Route::middleware(['role:renter'])->prefix('renter')->name('renter.')->group(function () {
        Route::view('/home', 'renter.dashboard')->name('dashboard');
        Route::get('/profile/setup', [ProfileController::class, 'editRenterStore'])->name('profile.setup');

        Route::get('/costumes/manage', [CostumeController::class, 'index'])->name('costumes.manage');
        Route::view('/costumes/upload', 'renter.upload_costume')->name('costumes.upload');

        Route::view('/orders', 'renter.view_orders')->name('orders');
    });

    // --- USER ROUTES (Accessible by any logged-in user, including Renter/Admin) ---
    Route::view('/costume/detail/{id}', 'user.costume_detail')->name('costume.detail');
    Route::get('/order/place/{costume_id}', [OrderController::class, 'createOrder'])->name('order.place');
    Route::post('/order/store', [OrderController::class, 'storeOrder'])->name('order.store');
    Route::get('/my-orders', [UserOrderController::class, 'index'])->name('user.orders');
});

require __DIR__ . '/auth.php';