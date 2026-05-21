<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    // =========================
    // GET ADDRESS
    // =========================
    public function index()
    {
        try {

            $data = DB::table('addresses')
                ->where('user_id', 1)
                ->orderByDesc('is_default')
                ->get();

            return response()->json($data);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // STORE ADDRESS
    // =========================
    public function store(Request $request)
    {
        try {

            $request->validate([
                'label' => 'required|string',
                'recipient_name' => 'required|string',
                'phone' => 'required|string',
                'address_line' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'required|string',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            // reset default
            DB::table('addresses')
                ->where('user_id', 1)
                ->update([
                    'is_default' => false
                ]);

            DB::table('addresses')->insert([
                'user_id' => 1,

                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'phone' => $request->phone,
                'address_line' => $request->address_line,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,

                'is_default' => true,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil disimpan'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}