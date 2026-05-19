<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        DB::table('addresses')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Alamat berhasil disimpan'
        ]);
    }

    public function index()
    {
        $data = DB::table('addresses')->get();

        return response()->json($data);
    }
}