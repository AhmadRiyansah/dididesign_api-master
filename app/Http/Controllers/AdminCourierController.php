<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCourierController extends Controller
{
    /**
     * Tampilkan daftar semua kurir.
     */
    public function index()
    {
        $couriers = Courier::with(['user.profile'])
            ->latest()
            ->paginate(15);

        return view('admin.couriers.index', compact('couriers'));
    }

    /**
     * Tampilkan form tambah kurir baru.
     */
    public function create()
    {
        return view('admin.couriers.create');
    }

    /**
     * Simpan kurir baru (buat user + profile + courier).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|string|min:8',
            'vehicle_type'  => 'nullable|string|max:50',
            'plate_number'  => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role'     => UserRole::Kurir,
            ]);

            $user->profile()->create([
                'name'  => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]);

            Courier::create([
                'user_id'      => $user->id,
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'plate_number' => $validated['plate_number'] ?? null,
                'is_available' => false,
            ]);
        });

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Kurir berhasil ditambahkan!');
    }

    /**
     * Toggle status aktif/nonaktif kurir (via PATCH).
     */
    public function toggleAvailability(Courier $courier)
    {
        $courier->update([
            'is_available' => !$courier->is_available,
        ]);

        $status = $courier->is_available ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.couriers.index')
            ->with('success', "Kurir berhasil {$status}!");
    }

    /**
     * Hapus kurir (beserta user & profile).
     */
    public function destroy(Courier $courier)
    {
        $user = $courier->user;

        // Hapus dokumen KTP & foto kendaraan jika ada
        if ($courier->ktp_path) {
            Storage::disk('public')->delete($courier->ktp_path);
        }
        if ($courier->vehicle_photo_path) {
            Storage::disk('public')->delete($courier->vehicle_photo_path);
        }

        $courier->delete();

        // Hapus user dan profile juga
        if ($user) {
            $user->profile?->delete();
            $user->delete();
        }

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Kurir berhasil dihapus!');
    }
}
