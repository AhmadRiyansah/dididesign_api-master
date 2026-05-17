<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourierController extends Controller
{
    public function index(): JsonResponse
    {
        $couriers = Courier::with(['user:id,email,role,firebase_uid', 'user.profile'])
            ->latest()
            ->get();

        return response()->json(['data' => $couriers]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'nullable|string|min:8',
            'vehicle_type'  => 'nullable|string|max:50',
            'plate_number'  => 'nullable|string|max:20',
            'firebase_uid'  => 'nullable|string|max:128|unique:users,firebase_uid',
        ]);

        $courier = DB::transaction(function () use ($validated) {
            $user = User::create([
                'email'        => $validated['email'],
                'password'     => bcrypt($validated['password'] ?? Str::random(16)),
                'role'         => UserRole::Kurir,
                'firebase_uid' => $validated['firebase_uid'] ?? null,
            ]);

            $user->profile()->create([
                'name'  => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]);

            return Courier::create([
                'user_id'       => $user->id,
                'vehicle_type'  => $validated['vehicle_type'] ?? null,
                'plate_number'  => $validated['plate_number'] ?? null,
                'is_available'  => false,
            ])->load(['user.profile']);
        });

        return response()->json([
            'message' => 'Kurir berhasil dibuat',
            'data'    => $courier,
        ], 201);
    }

    public function updateAvailability(Request $request, Courier $courier): JsonResponse
    {
        $validated = $request->validate([
            'is_available' => 'required|boolean',
            'current_lat'  => 'nullable|numeric|between:-90,90',
            'current_lng'  => 'nullable|numeric|between:-180,180',
        ]);

        $courier->update($validated);

        return response()->json([
            'message' => 'Status kurir diperbarui',
            'data'    => $courier->fresh(['user.profile']),
        ]);
    }
}
