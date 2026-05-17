<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierOrderController extends Controller
{
    /**
     * Daftar pesanan milik kurir yang sedang login.
     * Query: ?status=assigned|shipping|done
     *   - assigned  = pesanan baru di-assign, belum di-accept
     *   - shipping  = kurir sudah accept & sedang antar
     *   - done      = selesai diantar
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Order::where('courier_id', $user->id)
            ->with(['items.product', 'customer.profile'])
            ->latest();

        $status = $request->query('status');

        if ($status === 'assigned') {
            // Pesanan baru di-assign, belum di-accept kurir
            $query->where('order_status', 'process');
        } elseif ($status === 'shipping') {
            $query->where('order_status', 'shipping');
        } elseif ($status === 'done') {
            $query->where('order_status', 'done');
        }

        $orders = $query->get()->map(fn ($o) => $this->formatCourierOrder($o));

        return response()->json(['data' => $orders]);
    }

    /**
     * Kurir menerima pesanan → order_status jadi 'shipping'.
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $order = Order::where('courier_id', $user->id)->findOrFail($id);

        if ($order->order_status !== 'process') {
            return response()->json(['message' => 'Pesanan tidak dapat diterima.'], 422);
        }

        $order->update(['order_status' => 'shipping']);

        // Notifikasi ke pelanggan
        Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Pesanan Sedang Dikirim',
            'body'    => "Kurir sedang menuju lokasi Anda untuk pesanan {$order->order_code}.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id, 'order_code' => $order->order_code]),
        ]);

        return response()->json([
            'message' => 'Pesanan diterima. Silakan jemput barang.',
            'data'    => $this->formatCourierOrder($order->fresh(['items.product', 'customer.profile'])),
        ]);
    }

    /**
     * Kurir sudah jemput barang di toko.
     */
    public function pickup(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $order = Order::where('courier_id', $user->id)->findOrFail($id);

        if ($order->order_status !== 'shipping') {
            return response()->json(['message' => 'Status pesanan tidak valid.'], 422);
        }

        // Update shipment jika ada
        if ($order->shipment) {
            $order->shipment->update([
                'status'       => 'picked_up',
                'picked_up_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Barang berhasil dijemput. Antar ke pelanggan.',
        ]);
    }

    /**
     * Kurir sudah antar ke pelanggan → order_status jadi 'done'.
     */
    public function deliver(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $order = Order::where('courier_id', $user->id)->findOrFail($id);

        if ($order->order_status !== 'shipping') {
            return response()->json(['message' => 'Status pesanan tidak valid.'], 422);
        }

        $order->update([
            'order_status'   => 'done',
            'payment_status' => 'paid',
        ]);

        // Update shipment
        if ($order->shipment) {
            $order->shipment->update([
                'status'       => 'delivered',
                'delivered_at' => now(),
            ]);
        }

        // Notifikasi ke pelanggan
        Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Pesanan Telah Sampai',
            'body'    => "Pesanan {$order->order_code} telah diantar. Terima kasih!",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id]),
        ]);

        return response()->json([
            'message' => 'Pesanan selesai diantar.',
            'data'    => $this->formatCourierOrder($order->fresh(['items.product', 'customer.profile'])),
        ]);
    }

    /**
     * Kurir menolak pesanan → unassign, cari kurir berikutnya.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $order = Order::where('courier_id', $user->id)->findOrFail($id);

        if ($order->order_status !== 'process') {
            return response()->json(['message' => 'Pesanan tidak dapat ditolak.'], 422);
        }

        // Unassign kurir ini
        $order->update(['courier_id' => null]);

        // Coba auto-assign ke kurir berikutnya (exclude kurir yang baru tolak)
        $excludeIds = $request->input('excluded_couriers', []);
        $excludeIds[] = $user->id;

        $assigned = self::autoAssignCourier($order, $excludeIds);

        if ($assigned) {
            return response()->json([
                'message' => 'Pesanan dialihkan ke kurir lain.',
            ]);
        }

        // Semua kurir gagal → masuk admin panel (unassigned)
        Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Mencari Kurir',
            'body'    => "Sedang mencari kurir pengganti untuk pesanan {$order->order_code}.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode(['order_id' => $order->id]),
        ]);

        return response()->json([
            'message' => 'Tidak ada kurir tersedia. Pesanan masuk ke admin.',
        ]);
    }

    // ── Auto-Assign Logic ─────────────────────────────────────────────────

    /**
     * Cari kurir online terdekat → assign ke order.
     * Mengembalikan true jika berhasil assign, false jika tidak ada kurir.
     *
     * @param Order $order    Pesanan yang perlu kurir
     * @param array $exclude  ID user kurir yang sudah menolak
     */
    public static function autoAssignCourier(Order $order, array $exclude = []): bool
    {
        // Ambil koordinat tujuan dari alamat order
        // Untuk saat ini gunakan koordinat default Lhokseumawe jika order tidak punya lat/lng
        $destLat = 5.1797;
        $destLng = 97.1497;

        // Cari semua kurir yang online & available, exclude yang sudah tolak
        $couriers = Courier::where('is_available', true)
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->when(!empty($exclude), function ($q) use ($exclude) {
                $q->whereNotIn('user_id', $exclude);
            })
            ->get();

        if ($couriers->isEmpty()) {
            return false;
        }

        // Hitung jarak dan urutkan dari terdekat
        $couriers = $couriers->map(function ($courier) use ($destLat, $destLng) {
            $courier->distance = self::haversine(
                $courier->current_lat,
                $courier->current_lng,
                $destLat,
                $destLng
            );
            return $courier;
        })->sortBy('distance');

        // Assign ke kurir terdekat
        $nearest = $couriers->first();
        $order->update(['courier_id' => $nearest->user_id]);

        // Notifikasi ke kurir
        Notification::create([
            'user_id' => $nearest->user_id,
            'title'   => 'Pesanan Baru Masuk!',
            'body'    => "Anda mendapat pesanan {$order->order_code}. Segera terima atau tolak.",
            'type'    => 'order',
            'is_read' => false,
            'data'    => json_encode([
                'order_id'   => $order->id,
                'order_code' => $order->order_code,
                'distance'   => round($nearest->distance, 1),
            ]),
        ]);

        return true;
    }

    /**
     * Haversine formula → jarak antara 2 titik koordinat (dalam km).
     */
    private static function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // ── Format Helper ─────────────────────────────────────────────────────

    private function formatCourierOrder(Order $order): array
    {
        $customer = $order->customer;
        $profile  = $customer?->profile;

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
            'customer'       => [
                'name'  => $profile?->name ?? 'Pelanggan',
                'phone' => $profile?->phone,
            ],
            'items' => $order->items->map(fn ($i) => [
                'id'            => $i->id,
                'quantity'      => $i->quantity,
                'price'         => (float) $i->price,
                'subtotal'      => (float) $i->subtotal,
                'product_name'  => $i->product?->name,
                'product_image' => $i->product?->image,
            ])->toArray(),
        ];
    }
}
