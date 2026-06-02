<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PrintOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PrintOrderController extends Controller
{
    // ── Tabel Harga ──────────────────────────────────────────────────────────

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
            case 'spanduk':
                $area      = $widthMeter * $heightMeter;
                $unitPrice = 25000 * $area;
                $total     = $unitPrice * $quantity;
                return ['unit_price' => $unitPrice, 'binding_cost' => 0, 'total_price' => $total];

            case 'undangan':
                $unitPrice = $inkType === 'bw' ? 2000 : 6000;
                break;

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

            case 'kartu_nama':
                $boxes     = ceil($quantity / 100);
                $unitPrice = 85000;
                $total     = $unitPrice * $boxes;
                return ['unit_price' => $unitPrice, 'binding_cost' => 0, 'total_price' => $total];

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

        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('print-orders/' . $user->id, 'public');
            $fileName = $request->file('file')->getClientOriginalName();
        }

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

    public function index(Request $request): JsonResponse
    {
        $orders = PrintOrder::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn ($o) => $this->formatOrder($o));

        return response()->json(['data' => $orders]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = PrintOrder::where('user_id', $request->user()->id)->findOrFail($id);
        return response()->json(['data' => $this->formatOrder($order)]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = PrintOrder::where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->order_status !== 'process') {
            return response()->json(['message' => 'Pesanan tidak dapat dibatalkan.'], 422);
        }

        $order->update(['order_status' => 'cancel']);

        return response()->json(['message' => 'Pesanan cetak berhasil dibatalkan.']);
    }

    /**
     * Generate Snap Token untuk Payment (WebView).
     * POST /api/print-orders/{id}/qris-token
     * Return: snap_token untuk buka halaman Midtrans Snap
     */
    public function generateQrisToken(Request $request, int $id): JsonResponse
    {
        try {
            // 1. Setup Config Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            // 2. Ambil Order
            $order = PrintOrder::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->first();
            
            if (!$order) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // 3. Siapkan Parameter Snap
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_code,
                    'gross_amount' => (int) $order->total_price,
                ],
                'item_details' => [[
                    'id' => $order->service_type,
                    'price' => (int) $order->total_price,
                    'quantity' => 1,
                    'name' => "Order " . $order->order_code,
                ]],
                'customer_details' => [
                    'first_name' => $request->user()->name ?? 'User',
                    'email' => $request->user()->email ?? 'user@example.com',
                ],
            ];

            // 4. Request Token ke Midtrans (Snap API)
            $snapToken = Snap::createTransaction($params)->token;

            return response()->json([
                'status' => 'success',
                'data' => ['snap_token' => $snapToken]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal generate token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get QRIS payment string for Midtrans Simulator.
     * POST /api/print-orders/{id}/qris-string
     */
    public function getQrisString(Request $request, int $id): JsonResponse
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        
        $order = PrintOrder::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/{$order->order_code}/qris",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode(config('services.midtrans.server_key') . ':'),
            ],
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200 || $httpCode == 201) {
            $data = json_decode($response, true);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'qr_string' => $data['qr_string'] ?? null,
                    'actions' => $data['actions'] ?? null,
                ]
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan QRIS string',
            'response' => json_decode($response, true),
        ], 500);
    }

    /**
     * Cek status pembayaran order.
     * GET /api/print-orders/{orderCode}/payment-status
     */
    public function checkPaymentStatus(Request $request, string $orderCode): JsonResponse
    {
        $order = PrintOrder::where('order_code', $orderCode)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        
        return response()->json([
            'status' => $order->payment_status,
            'is_paid' => $order->payment_status === 'success',
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function formatOrder(PrintOrder $order): array
    {
        return [
            'id'             => $order->id,
            'order_code'     => $order->order_code,
            'service_type'   => $order->service_type,
            'service_label'  => $order->service_label,
            'file_name'      => $order->file_name,
            'file_url'       => $order->file_path ? asset('storage/' . $order->file_path) : null,
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