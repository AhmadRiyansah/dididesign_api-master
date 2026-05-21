<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CourierDashboardController extends Controller
{
    /**
     * Get Courier Dashboard Stats & Active Task
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $courier = $user->courier;

        if (!$courier) {
            return response()->json(['message' => 'Profil kurir tidak ditemukan'], 404);
        }

        // 1. Pesanan Selesai & Pendapatan Hari Ini
        $today = Carbon::today();
        
        $completedOrdersToday = Order::where('courier_id', $user->id)
            ->where('order_status', 'done')
            ->whereDate('updated_at', $today)
            ->get();

        $pesananSelesai = $completedOrdersToday->count();
        // Asumsi pendapatan dari shipping_cost
        $pendapatanHariIni = $completedOrdersToday->sum('shipping_cost');

        // 2. Rating (Dummy for now or calculated if reviews exist)
        $rating = 4.9;

        // 3. Tugas Aktif (Order with status 'assigned' or 'shipping')
        $activeTask = Order::where('courier_id', $user->id)
            ->whereIn('order_status', ['assigned', 'shipping'])
            ->with(['items.product'])
            ->latest()
            ->first();

        // 4. Jam Online (Simulated active hours)
        $jamOnline = "0j 0m"; 
        if ($courier->is_available) {
            $jamOnline = "Sedang Aktif";
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pendapatan_hari_ini' => $pendapatanHariIni,
                'pesanan_selesai' => $pesananSelesai,
                'jam_online' => $jamOnline,
                'rating' => $rating,
                'is_available' => $courier->is_available,
                'active_task' => $activeTask
            ]
        ]);
    }

    /**
     * Get Courier Documents
     */
    public function getDocuments(Request $request)
    {
        $courier = $request->user()->courier;

        if (!$courier) {
            return response()->json(['message' => 'Profil kurir tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle_type' => $courier->vehicle_type,
                'plate_number' => $courier->plate_number,
                'ktp_url' => $courier->ktp_path ? asset('storage/' . $courier->ktp_path) : null,
                'vehicle_photo_url' => $courier->vehicle_photo_path ? asset('storage/' . $courier->vehicle_photo_path) : null,
                'ktp_path' => $courier->ktp_path,
                'vehicle_photo_path' => $courier->vehicle_photo_path,
            ]
        ]);
    }

    /**
     * Upload / Update Courier Documents
     */
    public function updateDocuments(Request $request)
    {
        $courier = $request->user()->courier;

        if (!$courier) {
            return response()->json(['message' => 'Profil kurir tidak ditemukan'], 404);
        }

        $request->validate([
            'vehicle_type' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:50',
            'ktp_image' => 'nullable|image|max:5120', // max 5MB
            'vehicle_image' => 'nullable|image|max:5120',
        ]);

        if ($request->has('vehicle_type')) {
            $courier->vehicle_type = $request->vehicle_type;
        }

        if ($request->has('plate_number')) {
            $courier->plate_number = $request->plate_number;
        }

        if ($request->hasFile('ktp_image')) {
            if ($courier->ktp_path && Storage::disk('public')->exists($courier->ktp_path)) {
                Storage::disk('public')->delete($courier->ktp_path);
            }
            $courier->ktp_path = $request->file('ktp_image')->store('couriers/ktp', 'public');
        }

        if ($request->hasFile('vehicle_image')) {
            if ($courier->vehicle_photo_path && Storage::disk('public')->exists($courier->vehicle_photo_path)) {
                Storage::disk('public')->delete($courier->vehicle_photo_path);
            }
            $courier->vehicle_photo_path = $request->file('vehicle_image')->store('couriers/vehicle', 'public');
        }

        $courier->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diperbarui',
            'data' => [
                'vehicle_type' => $courier->vehicle_type,
                'plate_number' => $courier->plate_number,
                'ktp_url' => $courier->ktp_path ? asset('storage/' . $courier->ktp_path) : null,
                'vehicle_photo_url' => $courier->vehicle_photo_path ? asset('storage/' . $courier->vehicle_photo_path) : null,
            ]
        ]);
    }
}
