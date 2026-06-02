<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    /**
     * Generate QRIS Snap Token.
     * POST /api/payment/qris
     */
    public function generateQris(Request $request): JsonResponse
    {
        try {
            // 1. Validasi input
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'order_id' => 'required|string',
            ]);

            // 2. Setup Midtrans Config
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            // 3. Cari order berdasarkan order_id (pakai order_code)
            $order = Order::where('order_code', $validated['order_id'])
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order tidak ditemukan',
                ], 404);
            }

            // 4. Siapkan parameter transaksi untuk Snap
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_code,
                    'gross_amount' => (int) $validated['amount'],
                ],
                'item_details' => $this->getOrderItems($order),
                'customer_details' => [
                    'first_name' => $request->user()->name ?? 'Customer',
                    'email' => $request->user()->email ?? 'customer@example.com',
                ],
            ];

            // 5. Generate Snap Token
            $snapToken = Snap::createTransaction($params)->token;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'snap_token' => $snapToken,
                    'order_id' => $order->order_code,
                    'amount' => $validated['amount'],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal generate QRIS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order items for Midtrans.
     */
    private function getOrderItems(Order $order): array
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name ?? 'Product',
            ];
        }

        return $items;
    }
}