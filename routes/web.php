<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCourierController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminReportController;
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
        Route::get('/produk', [AdminProductController::class, 'index'])->name('produk.index');
        Route::get('/produk/create', [AdminProductController::class, 'create'])->name('produk.create');
        Route::post('/produk', [AdminProductController::class, 'store'])->name('produk.store');
        Route::get('/produk/{product}/edit', [AdminProductController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{product}', [AdminProductController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{product}', [AdminProductController::class, 'destroy'])->name('produk.destroy');
        

        
        // Manajemen Kategori
        Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('kategori.destroy');

        // Manajemen Kurir
        Route::get('/kurir',          [AdminCourierController::class, 'index'])->name('kurir.index');
        Route::get('/kurir/create',   [AdminCourierController::class, 'create'])->name('kurir.create');
        Route::post('/kurir',         [AdminCourierController::class, 'store'])->name('kurir.store');
        Route::patch('/kurir/{courier}/toggle', [AdminCourierController::class, 'toggleAvailability'])->name('kurir.toggle');
        Route::delete('/kurir/{courier}', [AdminCourierController::class, 'destroy'])->name('kurir.destroy');

        // Manajemen Pesanan
        Route::get('/pesanan',                      [AdminOrderController::class, 'index'])->name('pesanan.index');
        Route::patch('/pesanan/{order}/assign',      [AdminOrderController::class, 'assignCourier'])->name('pesanan.assign');
        Route::patch('/pesanan/{order}/status',      [AdminOrderController::class, 'updateStatus'])->name('pesanan.status');

        // Manajemen Pembayaran
        Route::get('/pembayaran', [AdminPaymentController::class, 'index'])->name('pembayaran.index');

        // Manajemen Laporan
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan.index');

        // Manajemen Cetak File
        Route::get('/cetak-file',                [\App\Http\Controllers\AdminPrintOrderController::class, 'index'])->name('print-orders.index');
        Route::patch('/cetak-file/{printOrder}/status', [\App\Http\Controllers\AdminPrintOrderController::class, 'updateStatus'])->name('print-orders.status');


        // Manajemen Pengguna
        Route::get('/pengguna',                       [\App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/pengguna/{user}',             [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
