<?php

// routes/web.php (The Final Route Structure)

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| The front page is the Catalog, accessible to everyone.
*/

// CHANGE: Root path ("/") now points to the Catalog view.
Route::get('/', function () {
    return view('catalog');
})->name('catalog');

/*
|--------------------------------------------------------------------------
| Authenticated & Role-Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // --- STANDARD BREEZE PROFILE ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MAIN REDIRECT LOGIC (Dashboard) ---
    Route::get('/dashboard', function () {
        if (Auth::user()->hasRole('admin')) {
            // Admin must use the admin dashboard route name
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->hasRole('renter')) {
            // Renter must use the renter dashboard route name
            return redirect()->route('renter.dashboard');
        }
        // User (Regular Customer) goes to the Catalog (which is '/')
        return redirect()->route('catalog');
    })->name('dashboard');

    // --- ADMIN ROUTES (Protected) ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::view('/home', 'admin.dashboard')->name('dashboard'); // Dedicated dashboard route
        Route::view('/users', 'admin.manage_users')->name('users');
        Route::view('/costumes', 'admin.manage_costumes')->name('costumes');
        Route::view('/transactions', 'admin.monitor_transactions')->name('transactions');
    });

    // --- RENTER ROUTES (Protected) ---
    Route::middleware(['role:renter'])->prefix('renter')->name('renter.')->group(function () {
        Route::view('/home', 'renter.dashboard')->name('dashboard'); // Dedicated dashboard route
        Route::view('/profile/setup', 'renter.store_profile_setup')->name('profile.setup');
        Route::view('/costumes/upload', 'renter.upload_costume')->name('costumes.upload');
        Route::view('/orders', 'renter.view_orders')->name('orders');
    });

    // --- USER ROUTES (Accessible by any logged-in user, including Renter/Admin) ---
    Route::view('/costume/detail/{id}', 'user.costume_detail')->name('costume.detail');
    Route::view('/order/place/{costume_id}', 'user.place_order')->name('order.place'); // Added {costume_id} for context
    Route::view('/my-orders', 'user.track_orders')->name('user.orders');
});

// The authentication routes (login, register, logout)
require __DIR__ . '/auth.php';