<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Daftar pesanan milik user yang sedang login.
     * Query: ?status=process|shipping|done|cancel
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::where('user_id', $request->user()->id)
            ->with(['items.product'])
            ->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->get()->map(fn ($o) => $this->formatOrder($o));

        return response()->json(['data' => $orders]);
    }

    /**
     * Detail satu pesanan.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items.product'])
            ->findOrFail($id);

        return response()->json(['data' => $this->formatOrder($order)]);
    }

    /**
     * Buat pesanan baru dari keranjang.
     * Body: { payment_method, address }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cod,transfer',
            'address'        => 'required|string|max:500',
        ]);

        $user = $request->user();

        // Ambil keranjang
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        // Hitung total
        $totalPrice   = $cart->items->sum('subtotal');
        $shippingCost = 0; // gratis ongkir untuk saat ini
        $grandTotal   = $totalPrice + $shippingCost;

        // Buat order
        $order = Order::create([
            'user_id'        => $user->id,
            'order_code'     => 'ORD-' . strtoupper(Str::random(8)),
            'total_price'    => $totalPrice,
            'shipping_cost'  => $shippingCost,
            'grand_total'    => $grandTotal,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'order_status'   => 'process',
            'address'        => $validated['address'],
        ]);

        // Salin items dari keranjang ke order_items
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity'   => $cartItem->quantity,
                'price'      => $cartItem->product->price,
                'subtotal'   => $cartItem->subtotal,
            ]);
        }

        // Kosongkan keranjang
        $cart->items()->delete();

        // Buat notifikasi untuk user
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Pesanan Berhasil Dibuat',
            'body'    => "Pesanan {$order->order_code} sedang diproses. Total: Rp " . number_format($grandTotal, 0, ',', '.'),
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id, 'order_code' => $order->order_code]),
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'data'    => $this->formatOrder($order->load('items.product')),
        ], 201);
    }

    /**
     * Batalkan pesanan (hanya jika status masih 'process').
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->order_status !== 'process') {
            return response()->json(['message' => 'Pesanan tidak dapat dibatalkan.'], 422);
        }

        $order->update(['order_status' => 'cancel']);

        // Notifikasi pembatalan
        Notification::create([
            'user_id' => $request->user()->id,
            'title'   => 'Pesanan Dibatalkan',
            'body'    => "Pesanan {$order->order_code} telah dibatalkan.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id]),
        ]);

        return response()->json(['message' => 'Pesanan berhasil dibatalkan.']);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function formatOrder(Order $order): array
    {
        return [
            'id'             => $order->id,
            'order_code'     => $order->order_code,
            'total_price'    => (float) $order->total_price,
            'shipping_cost'  => (float) $order->shipping_cost,
            'grand_total'    => (float) $order->grand_total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status'   => $order->order_status,
            'address'        => $order->address,
            'created_at'     => $order->created_at?->toDateTimeString(),
            'items'          => $order->items->map(fn ($i) => [
                'id'           => $i->id,
                'quantity'     => $i->quantity,
                'price'        => (float) $i->price,
                'subtotal'     => (float) $i->subtotal,
                'product_name' => $i->product?->name,
                'product_image'=> $i->product?->image,
            ])->toArray(),
        ];
    }
}
