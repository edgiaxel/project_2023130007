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
use Spatie\Permission\Models\Role;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');
Route::view('/store/{user_id}', 'store.public_store')->name('public.store');
Route::get('captcha/{config?}', [Mews\Captcha\CaptchaController::class, 'getCaptcha'])->name('captcha.custom');

Route::middleware('auth')->group(function () {
    // --- STANDARD BREEZE PROFILE ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/renter/store', [ProfileController::class, 'updateRenterStore'])->name('renter.store.update');

    // Update the main dashboard redirection logic to prioritize the new Owner role.
    Route::get('/dashboard', function () {
        // Owner check added from previous instruction
        if (Auth::user()->hasRole('owner')) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->hasRole('renter')) {
            return redirect()->route('renter.dashboard');
        }
        return redirect()->route('catalog');
    })->name('dashboard');


    // --- OWNER-ONLY PERMISSION ROUTES (Layered protection) ---
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/owner/users/{user_id}/permissions', [AdminController::class, 'editUserPermissions'])->name('owner.users.permissions.edit');
        Route::post('/owner/users/{user_id}/permissions/sync', [AdminController::class, 'syncUserPermissions'])->name('owner.users.permissions.sync');
    });

    // --- ADMIN ROUTES (Protected by admin|owner role, permissions checked by middleware in the group) ---
    Route::middleware(['role:admin|owner'])->prefix('admin')->name('admin.')->group(function () {

        // ANALYTICS & DASHBOARD (Protected by specific permissions)
        Route::get('/home', [AdminController::class, 'dashboard'])->middleware('permission:platform:view-global-kpis')->name('dashboard');
        Route::get('/renters/{user_id}/analytics', [AdminController::class, 'viewRenterAnalytics'])->middleware('permission:analytics:view-renter')->name('renters.analytics');

        // MANAGEMENT 
        Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
        Route::get('/costumes/all', [AdminController::class, 'manageCostumes'])->name('costumes.manage');
        Route::get('/costumes/approval', [AdminController::class, 'approvalQueue'])->middleware('permission:costume:approve-reject')->name('costumes.approval');
        Route::post('/costumes/{costume_id}/set-approval', [AdminController::class, 'setCostumeApproval'])->middleware('permission:costume:approve-reject')->name('costumes.set_approval');

        // DISCOUNT/BANNER MANAGEMENT
        Route::get('/discounts', [AdminController::class, 'manageDiscounts'])->middleware('permission:discount:manage-global')->name('discounts.manage');
        Route::post('/discounts/global', [AdminController::class, 'setGlobalDiscount'])->middleware('permission:discount:manage-global')->name('discounts.global.set');
        Route::get('/transactions', [AdminController::class, 'monitorTransactions'])->name('transactions');
        Route::get('/banners', [AdminController::class, 'manageBanners'])->middleware('permission:platform:manage-banners')->name('banners.manage');
        Route::post('/banners/{id}', [AdminController::class, 'updateBanner'])->middleware('permission:platform:manage-banners')->name('banners.update');

        Route::get('/stores', [AdminController::class, 'manageRenterStores'])->name('stores.manage');
        Route::get('/stores/{user_id}/view', [AdminController::class, 'viewRenterStoreDetails'])->name('stores.view');

        // USER EDIT & ROLE UPDATE (Permissions handled in Controller/Middleware)
        Route::get('/users/{user_id}/edit', [AdminController::class, 'editUser'])->middleware('permission:user:edit-renter-user|user:manage-roles')->name('users.edit');
        Route::patch('/users/{user_id}/update', [AdminController::class, 'updateUser'])->middleware('permission:user:edit-renter-user|user:manage-roles')->name('users.update');
        Route::post('/users/{user_id}/update-role', [AdminController::class, 'updateRole'])->middleware('permission:user:edit-renter-user|user:manage-roles')->name('users.updateRole');

        // NEW: Route for Admin to update another user's RenterStore
        Route::patch('/stores/{user_id}/update-store', [AdminController::class, 'updateRenterStoreDetails'])->name('stores.update_details');
    });

    // --- RENTER ROUTES (Protected by renter|admin|owner role) ---
    Route::middleware(['role:renter|admin|owner'])->prefix('renter')->name('renter.')->group(function () {
        Route::view('/home', 'renter.dashboard')->name('dashboard');

        // **!!! THE MISSING ROUTE NAME IS FIXED HERE !!!**
        Route::get('/profile/setup', [ProfileController::class, 'editRenterStore'])->name('profile.setup');

        Route::get('/costumes/manage', [CostumeController::class, 'index'])->name('costumes.manage');

        // Costume CRUD
        Route::get('/costumes/upload', fn() => view('renter.upload_costume'))->middleware('permission:costume:create')->name('costumes.upload');
        Route::post('/costumes/store', [CostumeController::class, 'store'])->middleware('permission:costume:create')->name('costumes.store');
        Route::get('/costumes/{costume_id}/edit', [CostumeController::class, 'edit'])->middleware('permission:costume:edit-own|costume:edit-all')->name('costumes.edit');
        Route::patch('/costumes/{costume_id}/update', [CostumeController::class, 'update'])->middleware('permission:costume:edit-own|costume:edit-all')->name('costumes.update');
        Route::delete('/costumes/{costume_id}/delete', [CostumeController::class, 'destroy'])->middleware('permission:costume:delete-own|costume:delete-all')->name('costumes.delete');

        Route::view('/orders', 'renter.view_orders')->name('orders');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::post('/{order_id}/confirm', [OrderController::class, 'confirm'])->middleware('permission:order:confirm-reject')->name('confirm');
            Route::post('/{order_id}/reject', [OrderController::class, 'reject'])->middleware('permission:order:confirm-reject')->name('reject');
            Route::post('/{order_id}/update-status', [OrderController::class, 'updateStatus'])->middleware('permission:order:update-status')->name('update.status');
        });
    });

    // --- USER ROUTES (Accessible by any logged-in user, including Renter/Admin/Owner) ---
    Route::view('/costume/detail/{id}', 'user.costume_detail')->name('costume.detail');
    Route::get('/order/place/{costume_id}', [OrderController::class, 'createOrder'])->name('order.place');
    Route::post('/order/store', [OrderController::class, 'storeOrder'])->name('order.store');
    Route::get('/my-orders', [UserOrderController::class, 'index'])->name('user.orders');
    Route::get('/order/view/{order_id}', [OrderController::class, 'viewDetail'])->name('order.detail');
});

require __DIR__ . '/auth.php';