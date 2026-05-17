<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthSyncController extends Controller
{
    /**
     * Sinkronkan akun Firebase (mobile: pelanggan atau kurir).
     * - Pelanggan: simpan ke users + profiles
     * - Kurir: simpan ke users + profiles + couriers + upload dokumen
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firebase_uid'  => 'required|string|max:128',
            'email'         => 'required|email|max:255',
            'name'          => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'photo'         => 'nullable|string|max:500',
            'role'          => 'nullable|string|in:user,kurir,admin',
            // Data kurir
            'address'       => 'nullable|string|max:500',
            'vehicle_type'  => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20',
            // Upload file dokumen
            'ktp'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'vehicle_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $requestedRole = $this->resolveRole($validated['role'] ?? 'user');

        // ── Cari atau buat user ──────────────────────────────────────────
        $user = User::where('firebase_uid', $validated['firebase_uid'])->first()
            ?? User::where('email', $validated['email'])->first();

        if ($user) {
            $updateData = [
                'firebase_uid' => $validated['firebase_uid'],
                'email'        => $validated['email'],
            ];

            // Hanya update role jika dikirim secara eksplisit di request.
            // Saat login (sync tanpa 'role'), role yang sudah ada tidak boleh di-reset.
            if ($request->has('role')) {
                $updateData['role'] = $requestedRole;
            } else {
                // Gunakan role yang sudah ada di database
                $requestedRole = $user->role;
            }

            $user->update($updateData);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'  => $validated['name'] ?? $user->profile?->name ?? Str::before($validated['email'], '@'),
                    'phone' => $validated['phone'] ?? $user->profile?->phone,
                    'photo' => $validated['photo'] ?? $user->profile?->photo,
                ]
            );
        } else {
            $user = User::create([
                'firebase_uid' => $validated['firebase_uid'],
                'email'        => $validated['email'],
                'password'     => bcrypt(Str::random(32)),
                'role'         => $requestedRole,
            ]);

            $user->profile()->create([
                'name'  => $validated['name'] ?? Str::before($validated['email'], '@'),
                'phone' => $validated['phone'] ?? null,
                'photo' => $validated['photo'] ?? null,
            ]);
        }

        // ── Simpan data kurir jika role = kurir ──────────────────────────
        \Log::info('[Sync] requestedRole=' . $requestedRole->value . ' isKurir=' . ($requestedRole === UserRole::Kurir ? 'true' : 'false'));

        if ($requestedRole === UserRole::Kurir) {
            try {
                $courierData = [
                    'vehicle_type' => $validated['vehicle_type'] ?? null,
                    'plate_number' => $validated['license_plate'] ?? null,
                ];

                // Simpan foto KTP
                if ($request->hasFile('ktp')) {
                    $ktpPath = $request->file('ktp')->store('documents/ktp', 'public');
                    $courierData['ktp_path'] = $ktpPath;
                }

                // Simpan foto kendaraan
                if ($request->hasFile('vehicle_photo')) {
                    $vehiclePath = $request->file('vehicle_photo')->store('documents/vehicles', 'public');
                    $courierData['vehicle_photo_path'] = $vehiclePath;
                }

                \Log::info('[Sync] courier data to save', $courierData);

                Courier::updateOrCreate(
                    ['user_id' => $user->id],
                    $courierData
                );

                \Log::info('[Sync] Courier saved OK for user_id=' . $user->id);
            } catch (\Throwable $e) {
                \Log::error('[Sync] Courier save FAILED: ' . $e->getMessage());
                // Tetap lanjutkan — jangan gagalkan seluruh registrasi karena kurir
            }
        }

        $user->load('profile');

        $token = $user->createToken($user->role->value . '-mobile')->plainTextToken;

        return response()->json([
            'message' => 'User synced',
            'token'   => $token,
            'user'    => $user->makeHidden(['password']),
        ]);
    }

    /**
     * Resolve string role ke enum UserRole.
     */
    private function resolveRole(string $role): UserRole
    {
        return match ($role) {
            'kurir' => UserRole::Kurir,
            'admin' => UserRole::Admin,
            default => UserRole::User,
        };
    }
}
