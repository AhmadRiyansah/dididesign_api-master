<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Upload / ganti foto profil user yang sedang login.
     * Endpoint: POST /api/profile/photo  (auth:sanctum)
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user()->load('profile');

        // Hapus foto lama jika ada
        if ($user->profile?->photo) {
            Storage::disk('public')->delete($user->profile->photo);
        }

        // Simpan foto baru
        $path = $request->file('photo')->store('profiles', 'public');

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['photo' => $path]
        );

        $photoUrl = asset('storage/' . $path);

        return response()->json([
            'message'   => 'Foto profil diperbarui',
            'photo_url' => $photoUrl,
            'photo_path' => $path,
        ]);
    }

    /**
     * Update data profil (nama & nomor telepon).
     * Endpoint: PATCH /api/profile  (auth:sanctum)
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = $request->user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            array_filter($validated, fn($v) => $v !== null),
        );

        $user->load('profile');

        return response()->json([
            'message' => 'Profil diperbarui',
            'user'    => $user->makeHidden(['password']),
        ]);
    }
}
