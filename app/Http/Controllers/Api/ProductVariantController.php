<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ProductVariantController
 *
 * Mengelola CRUD produk beserta variannya dalam satu request.
 *
 * Best Practice:
 * - Gunakan DB::transaction() agar produk & varian tersimpan atomik
 *   (kalau varian gagal, produk juga dibatalkan)
 * - Validasi array varian menggunakan notasi "variants.*.field"
 * - Relasi Eloquent: Product hasMany ProductVariant
 */
class ProductVariantController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    // GET /api/v2/products
    // Tampilkan semua produk beserta variannya
    // ─────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        try {
            $products = Product::with(['category', 'images', 'variants'])
                ->latest()
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data'    => $products,
                'message' => 'Produk berhasil diambil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // GET /api/v2/products/{id}
    // Tampilkan satu produk beserta semua variannya
    // ─────────────────────────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'images', 'variants'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Produk berhasil diambil',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // POST /api/v2/products
    // Simpan produk + varian sekaligus dalam satu request
    //
    // Contoh Request JSON dari Flutter:
    // {
    //   "category_id": 1,
    //   "name": "Pulpen Standard",
    //   "description": "Pulpen berkualitas tinggi",
    //   "price": 5000,
    //   "stock": 100,
    //   "is_popular": true,
    //   "is_new_arrival": false,
    //   "variants": [
    //     { "nama_varian": "Hitam", "harga": 5000, "stok": 50 },
    //     { "nama_varian": "Biru",  "harga": 5000, "stok": 30 },
    //     { "nama_varian": "Merah", "harga": 6000, "stok": 20 }
    //   ]
    // }
    // ─────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        // Validasi produk + array varian
        $validated = $request->validate([
            'category_id'          => 'required|exists:categories,id',
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'stock'                => 'required|integer|min:0',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_popular'           => 'boolean',
            'is_new_arrival'       => 'boolean',

            // Validasi array varian — "variants.*" = setiap item dalam array
            'variants'             => 'nullable|array',
            'variants.*.nama_varian' => 'required_with:variants|string|max:100',
            'variants.*.harga'     => 'required_with:variants|numeric|min:0',
            'variants.*.stok'      => 'required_with:variants|integer|min:0',
        ]);

        try {
            // DB::transaction() — jika ada yang gagal, semua dibatalkan (rollback)
            $product = DB::transaction(function () use ($request, $validated) {

                // 1. Upload gambar jika ada
                if ($request->hasFile('image')) {
                    $validated['image'] = $request->file('image')->store('products', 'public');
                }

                // 2. Simpan produk utama
                $product = Product::create($validated);

                // 3. Simpan varian menggunakan foreach (loop)
                if (!empty($validated['variants'])) {
                    foreach ($validated['variants'] as $varianData) {
                        $product->variants()->create([
                            'nama_varian' => $varianData['nama_varian'],
                            'harga'       => $varianData['harga'],
                            'stok'        => $varianData['stok'],
                        ]);
                    }
                }

                return $product;
            });

            // Load relasi setelah disimpan untuk response
            $product->load(['category', 'variants']);

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Produk dan varian berhasil disimpan',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // PUT/PATCH /api/v2/products/{id}
    // Update produk dan sync variannya (hapus lama, simpan baru)
    //
    // Contoh Request JSON dari Flutter:
    // {
    //   "name": "Pulpen Standard Pro",
    //   "price": 7000,
    //   "variants": [
    //     { "nama_varian": "Hitam", "harga": 7000, "stok": 40 },
    //     { "nama_varian": "Emas",  "harga": 9000, "stok": 10 }
    //   ]
    // }
    // ─────────────────────────────────────────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id'            => 'exists:categories,id',
            'name'                   => 'string|max:255',
            'description'            => 'nullable|string',
            'price'                  => 'numeric|min:0',
            'stock'                  => 'integer|min:0',
            'image'                  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_popular'             => 'boolean',
            'is_new_arrival'         => 'boolean',
            'variants'               => 'nullable|array',
            'variants.*.nama_varian' => 'required_with:variants|string|max:100',
            'variants.*.harga'       => 'required_with:variants|numeric|min:0',
            'variants.*.stok'        => 'required_with:variants|integer|min:0',
        ]);

        try {
            $product = Product::findOrFail($id);

            $product = DB::transaction(function () use ($request, $validated, $product) {

                // 1. Upload gambar baru jika ada
                if ($request->hasFile('image')) {
                    // Hapus gambar lama
                    if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                        unlink(storage_path('app/public/' . $product->image));
                    }
                    $validated['image'] = $request->file('image')->store('products', 'public');
                }

                // 2. Update data produk utama
                $product->update($validated);

                // 3. Sync varian: hapus semua varian lama, simpan yang baru
                if (array_key_exists('variants', $validated)) {
                    // Hapus semua varian lama
                    $product->variants()->delete();

                    // Simpan varian baru menggunakan foreach
                    if (!empty($validated['variants'])) {
                        foreach ($validated['variants'] as $varianData) {
                            $product->variants()->create([
                                'nama_varian' => $varianData['nama_varian'],
                                'harga'       => $varianData['harga'],
                                'stok'        => $varianData['stok'],
                            ]);
                        }
                    }
                }

                return $product;
            });

            $product->load(['category', 'variants']);

            return response()->json([
                'success' => true,
                'data'    => $product,
                'message' => 'Produk berhasil diperbarui',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // DELETE /api/v2/products/{id}
    // Hapus produk beserta semua variannya (cascade via foreign key)
    // ─────────────────────────────────────────────────────────────────
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            // Hapus gambar dari storage
            if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                unlink(storage_path('app/public/' . $product->image));
            }

            // Hapus produk — varian otomatis terhapus karena onDelete('cascade')
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk dan semua variannya berhasil dihapus',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // GET /api/v2/products/{id}/variants
    // Tampilkan hanya daftar varian dari satu produk
    // ─────────────────────────────────────────────────────────────────
    public function variants(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $variants = $product->variants()->get();

            return response()->json([
                'success' => true,
                'data'    => $variants,
                'message' => "Varian produk '{$product->name}' berhasil diambil",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }
    }
}
