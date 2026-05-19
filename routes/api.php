<?php

use App\Http\Controllers\Api\Admin\CourierController as AdminCourierController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\AuthSyncController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PrintOrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| Public Routes (tidak perlu login)
|--------------------------------------------------------------------------
*/

Route::post('/auth/sync', [AuthSyncController::class, 'sync']);
Route::post('/auth/login', [LoginController::class, 'login']);

// Banner — public
Route::get('/banners', [BannerController::class, 'index']);

// Products — public (v1: tanpa varian)
Route::prefix('products')->group(function () {
    Route::get('/search',       [ProductController::class, 'search']);
    Route::get('/popular',      [ProductController::class, 'popular']);
    Route::get('/new-arrivals', [ProductController::class, 'newArrivals']);
    Route::get('/',             [ProductController::class, 'index']);
    Route::post('/',            [ProductController::class, 'store']);
    Route::get('/{id}',         [ProductController::class, 'show']);
    Route::put('/{id}',         [ProductController::class, 'update']);
    Route::patch('/{id}',       [ProductController::class, 'update']);
    Route::delete('/{id}',      [ProductController::class, 'destroy']);
});

// Products v2 — dengan dukungan varian (one-to-many)
// Endpoint: /api/v2/products
Route::prefix('v2/products')->group(function () {
    Route::get('/',              [ProductVariantController::class, 'index']);
    Route::post('/',             [ProductVariantController::class, 'store']);
    Route::get('/{id}',          [ProductVariantController::class, 'show']);
    Route::put('/{id}',          [ProductVariantController::class, 'update']);
    Route::patch('/{id}',        [ProductVariantController::class, 'update']);
    Route::delete('/{id}',       [ProductVariantController::class, 'destroy']);
    Route::get('/{id}/variants', [ProductVariantController::class, 'variants']); // hanya varian
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (perlu login — any role)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn (Request $request) => $request->user()->load('profile')->makeHidden(['password']));
    Route::post('/auth/logout', [LoginController::class, 'logout']);

    // Profile
    Route::patch('/profile',      [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto']);

    // Cart
    Route::get('/cart',               [CartController::class, 'index']);
    Route::post('/cart/items',        [CartController::class, 'addItem']);
    Route::patch('/cart/items/{id}',  [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);
    Route::delete('/cart',            [CartController::class, 'clear']);

    // Orders
    Route::get('/orders',               [OrderController::class, 'index']);
    Route::get('/orders/{id}',          [OrderController::class, 'show']);
    Route::post('/orders',              [OrderController::class, 'store']);
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Notifications
    Route::get('/notifications',             [NotificationController::class, 'index']);
    Route::patch('/notifications/read-all',  [NotificationController::class, 'markAllRead']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead']);

    // Print Orders
    Route::post('/print-orders/estimate',      [PrintOrderController::class, 'estimate']);
    Route::get('/print-orders',                [PrintOrderController::class, 'index']);
    Route::post('/print-orders',               [PrintOrderController::class, 'store']);
    Route::get('/print-orders/{id}',           [PrintOrderController::class, 'show']);
    Route::patch('/print-orders/{id}/cancel',  [PrintOrderController::class, 'cancel']);

});

/*
|--------------------------------------------------------------------------
| Users (mobile)
|--------------------------------------------------------------------------
*/ 

Route::post('/alamat', [AddressController::class, 'store']);
Route::get('/alamat', [AddressController::class, 'index']);



/*
|--------------------------------------------------------------------------
| Kurir (mobile)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:kurir'])->prefix('courier')->group(function () {
    Route::patch('/availability', function (Request $request) {
        $courier = $request->user()->courier;

        if (!$courier) {
            return response()->json(['message' => 'Profil kurir tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'is_available' => 'required|boolean',
            'current_lat'  => 'nullable|numeric|between:-90,90',
            'current_lng'  => 'nullable|numeric|between:-180,180',
        ]);

        $courier->update($validated);

        return response()->json([
            'message' => 'Status diperbarui',
            'data'    => $courier->fresh(),
        ]);
    });

    // Courier Order Management
    Route::get('/orders',                  [\App\Http\Controllers\Api\CourierOrderController::class, 'index']);
    Route::patch('/orders/{id}/accept',    [\App\Http\Controllers\Api\CourierOrderController::class, 'accept']);
    Route::patch('/orders/{id}/pickup',    [\App\Http\Controllers\Api\CourierOrderController::class, 'pickup']);
    Route::patch('/orders/{id}/deliver',   [\App\Http\Controllers\Api\CourierOrderController::class, 'deliver']);
    Route::patch('/orders/{id}/reject',    [\App\Http\Controllers\Api\CourierOrderController::class, 'reject']);
});

/*
|--------------------------------------------------------------------------
| Admin — manual assign kurir (fallback jika auto-assign gagal)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Existing courier management
    Route::get('/couriers',                          [AdminCourierController::class, 'index']);
    Route::post('/couriers',                         [AdminCourierController::class, 'store']);
    Route::patch('/couriers/{courier}/availability', [AdminCourierController::class, 'updateAvailability']);

    // Manual assign kurir ke pesanan
    Route::patch('/orders/{id}/assign-courier', function (Request $request, int $id) {
        $validated = $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $order = \App\Models\Order::findOrFail($id);
        $order->update(['courier_id' => $validated['courier_id']]);

        // Notifikasi ke kurir
        \App\Models\Notification::create([
            'user_id' => $validated['courier_id'],
            'title'   => 'Pesanan Baru (Admin)',
            'body'    => "Admin mengassign pesanan {$order->order_code} kepada Anda.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id, 'order_code' => $order->order_code]),
        ]);

        return response()->json([
            'message' => 'Kurir berhasil di-assign.',
            'data'    => $order->fresh(['items.product', 'courier.profile']),
        ]);
    });

    // Daftar pesanan yang belum punya kurir (unassigned)
    Route::get('/orders/unassigned', function () {
        $orders = \App\Models\Order::whereNull('courier_id')
            ->where('order_status', 'process')
            ->with(['items.product', 'customer.profile'])
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    });
});

