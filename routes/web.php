<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCourierController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login'));

// ── Alias 'login' agar middleware auth bisa redirect dengan benar ──
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('login');

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Produk
        Route::resource('products', AdminProductController::class);

        // Manajemen Kurir
        Route::get('/couriers',          [AdminCourierController::class, 'index'])->name('couriers.index');
        Route::get('/couriers/create',   [AdminCourierController::class, 'create'])->name('couriers.create');
        Route::post('/couriers',         [AdminCourierController::class, 'store'])->name('couriers.store');
        Route::patch('/couriers/{courier}/toggle', [AdminCourierController::class, 'toggleAvailability'])->name('couriers.toggle');
        Route::delete('/couriers/{courier}', [AdminCourierController::class, 'destroy'])->name('couriers.destroy');

        // Manajemen Pesanan
        Route::get('/orders',                      [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/assign',      [AdminOrderController::class, 'assignCourier'])->name('orders.assign');
        Route::patch('/orders/{order}/status',      [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        // Manajemen Cetak File
        Route::get('/print-orders',                [\App\Http\Controllers\AdminPrintOrderController::class, 'index'])->name('print-orders.index');
        Route::patch('/print-orders/{printOrder}/status', [\App\Http\Controllers\AdminPrintOrderController::class, 'updateStatus'])->name('print-orders.status');

        // Manajemen Pengguna
        Route::get('/users',                       [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}',             [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
