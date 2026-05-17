<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PrintOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrintOrderController extends Controller
{
    // ── Tabel Harga ──────────────────────────────────────────────────────────

    /**
     * Hitung harga berdasarkan service_type dan spesifikasi.
     * Return: [unit_price, binding_cost, total_price]
     */
    private function calculatePrice(array $data): array
    {
        $serviceType = $data['service_type'];
        $quantity    = (int) ($data['quantity'] ?? 1);
        $inkType     = $data['ink_type'] ?? 'color';
        $paperSize   = $data['paper_size'] ?? 'a4';
        $sides       = $data['sides'] ?? 'single';
        $binding     = $data['binding'] ?? 'none';
        $widthMeter  = (float) ($data['width_meter'] ?? 1);
        $heightMeter = (float) ($data['height_meter'] ?? 1);

        $unitPrice   = 0;
        $bindingCost = 0;

        switch ($serviceType) {

            // ── Spanduk: Rp 25.000 per m² ──────────────────────────────────
            case 'spanduk':
                $area      = $widthMeter * $heightMeter; // m²
                $unitPrice = 25000 * $area;
                $total     = $unitPrice * $quantity;
                return ['unit_price' => $unitPrice, 'binding_cost' => 0, 'total_price' => $total];

            // ── Undangan: Rp 2.000 – 10.000 (warna = mahal) ────────────────
            case 'undangan':
                $unitPrice = $inkType === 'bw' ? 2000 : 6000;
                break;

            // ── Brosur ──────────────────────────────────────────────────────
            case 'brosur':
                $sideMultiplier = $sides === 'double' ? 1.6 : 1.0;
                $priceMap = [
                    'a6' => ['bw' => 200,  'color' => 500],
                    'a5' => ['bw' => 650,  'color' => 1000],
                    'a4' => ['bw' => 1100, 'color' => 2600],
                ];
                $basePrice = $priceMap[$paperSize][$inkType] ?? 1100;
                $unitPrice = $basePrice * $sideMultiplier;
                break;

            // ── Kartu Nama: Rp 85.000 per box (100 lembar) ─────────────────
            case 'kartu_nama':
                $boxes     = ceil($quantity / 100);    // hitung per box
                $unitPrice = 85000;                    // per box
                $total     = $unitPrice * $boxes;
                return ['unit_price' => $unitPrice, 'binding_cost' => 0, 'total_price' => $total];

            // ── Cetak File (dokumen biasa) ──────────────────────────────────
            case 'cetak_file':
            default:
                $priceMap = [
                    'a4' => ['bw' => 500,  'color' => 1500],
                    'f4' => ['bw' => 600,  'color' => 1800],
                    'a3' => ['bw' => 1000, 'color' => 3000],
                ];
                $unitPrice = $priceMap[$paperSize][$inkType] ?? 500;
                break;
        }

        // Biaya penjilidan (untuk brosur, cetak_file, undangan)
        $bindingMap = [
            'none'       => 0,
            'staples'    => 2000,
            'spiral'     => 5000,
            'soft_cover' => 15000,
            'hard_cover' => 25000,
        ];
        $bindingCost = $bindingMap[$binding] ?? 0;
        $total       = ($unitPrice * $quantity) + $bindingCost;

        return [
            'unit_price'   => round($unitPrice, 2),
            'binding_cost' => $bindingCost,
            'total_price'  => round($total, 2),
        ];
    }

    // ── Endpoints ─────────────────────────────────────────────────────────────

    /**
     * Estimasi harga tanpa membuat order.
     * POST /api/print-orders/estimate
     */
    public function estimate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_type' => 'required|string|in:spanduk,undangan,brosur,kartu_nama,cetak_file',
            'paper_size'   => 'nullable|string',
            'ink_type'     => 'nullable|string|in:bw,color',
            'binding'      => 'nullable|string',
            'quantity'     => 'nullable|integer|min:1',
            'sides'        => 'nullable|string|in:single,double',
            'width_meter'  => 'nullable|numeric|min:0.1',
            'height_meter' => 'nullable|numeric|min:0.1',
        ]);

        $price = $this->calculatePrice($validated);

        return response()->json([
            'message' => 'Estimasi berhasil',
            'data'    => $price,
        ]);
    }

    /**
     * Buat pesanan cetak baru.
     * POST /api/print-orders
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_type'   => 'required|string|in:spanduk,undangan,brosur,kartu_nama,cetak_file',
            'paper_size'     => 'nullable|string',
            'ink_type'       => 'nullable|string|in:bw,color',
            'binding'        => 'nullable|string',
            'quantity'       => 'required|integer|min:1',
            'sides'          => 'nullable|string|in:single,double',
            'width_meter'    => 'nullable|numeric|min:0.1',
            'height_meter'   => 'nullable|numeric|min:0.1',
            'payment_method' => 'required|string|in:qris,transfer',
            'notes'          => 'nullable|string|max:500',
            'file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = $request->user();

        // Upload file jika ada
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('print-orders/' . $user->id, 'public');
            $fileName = $request->file('file')->getClientOriginalName();
        }

        // Hitung harga
        $price = $this->calculatePrice($validated);

        $order = PrintOrder::create([
            'user_id'        => $user->id,
            'order_code'     => 'PRT-' . strtoupper(Str::random(8)),
            'service_type'   => $validated['service_type'],
            'file_path'      => $filePath,
            'file_name'      => $fileName,
            'paper_size'     => $validated['paper_size'] ?? null,
            'ink_type'       => $validated['ink_type'] ?? null,
            'binding'        => $validated['binding'] ?? 'none',
            'quantity'       => $validated['quantity'],
            'sides'          => $validated['sides'] ?? 'single',
            'width_meter'    => $validated['width_meter'] ?? null,
            'height_meter'   => $validated['height_meter'] ?? null,
            'unit_price'     => $price['unit_price'],
            'binding_cost'   => $price['binding_cost'],
            'total_price'    => $price['total_price'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'order_status'   => 'process',
            'notes'          => $validated['notes'] ?? null,
        ]);

        // Notifikasi
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Pesanan Cetak Dibuat',
            'body'    => "Pesanan cetak {$order->order_code} sedang diproses. Total: Rp " . number_format($order->total_price, 0, ',', '.'),
            'type'    => 'print_order',
            'is_read' => false,
            'data'    => json_encode(['print_order_id' => $order->id, 'order_code' => $order->order_code]),
        ]);

        return response()->json([
            'message' => 'Pesanan cetak berhasil dibuat.',
            'data'    => $this->formatOrder($order),
        ], 201);
    }

    /**
     * Daftar pesanan cetak milik user.
     * GET /api/print-orders
     */
    public function index(Request $request): JsonResponse
    {
        $orders = PrintOrder::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatOrder($o));

        return response()->json(['data' => $orders]);
    }

    /**
     * Detail pesanan cetak.
     * GET /api/print-orders/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = PrintOrder::where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json(['data' => $this->formatOrder($order)]);
    }

    /**
     * Batalkan pesanan cetak.
     * PATCH /api/print-orders/{id}/cancel
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = PrintOrder::where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->order_status !== 'process') {
            return response()->json(['message' => 'Pesanan tidak dapat dibatalkan.'], 422);
        }

        $order->update(['order_status' => 'cancel']);

        return response()->json(['message' => 'Pesanan cetak berhasil dibatalkan.']);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function formatOrder(PrintOrder $order): array
    {
        return [
            'id'             => $order->id,
            'order_code'     => $order->order_code,
            'service_type'   => $order->service_type,
            'service_label'  => $order->service_label,
            'file_name'      => $order->file_name,
            'file_url'       => $order->file_path
                                ? asset('storage/' . $order->file_path)
                                : null,
            'paper_size'     => $order->paper_size,
            'ink_type'       => $order->ink_type,
            'binding'        => $order->binding,
            'quantity'       => $order->quantity,
            'sides'          => $order->sides,
            'width_meter'    => $order->width_meter,
            'height_meter'   => $order->height_meter,
            'unit_price'     => (float) $order->unit_price,
            'binding_cost'   => (float) $order->binding_cost,
            'total_price'    => (float) $order->total_price,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status'   => $order->order_status,
            'status_label'   => $order->status_label,
            'notes'          => $order->notes,
            'created_at'     => $order->created_at?->toDateTimeString(),
        ];
    }
}
