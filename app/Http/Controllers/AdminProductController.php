<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'variants')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'              => 'required|exists:categories,id',
            'name'                     => 'required|string|max:255|unique:products',
            'description'              => 'nullable|string',
            'price'                    => 'required|numeric|min:0',
            'stock'                    => 'required|integer|min:0',
            'image'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'is_popular'               => 'boolean',
            'is_new_arrival'           => 'boolean',
            // Validasi array varian dari form
            'variants'                 => 'nullable|array',
            'variants.*.nama_varian'   => 'required_with:variants|string|max:100',
            'variants.*.harga'         => 'required_with:variants|numeric|min:0',
            'variants.*.stok'          => 'required_with:variants|integer|min:0',
            'variants.*.image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['category_id', 'name', 'description', 'price', 'stock']);
            $data['is_popular']     = $request->boolean('is_popular');
            $data['is_new_arrival'] = $request->boolean('is_new_arrival');

            // Upload gambar utama produk
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($data);

            // Simpan setiap varian beserta fotonya
            $variants = $request->input('variants', []);
            $variantFiles = $request->file('variants', []);

            foreach ($variants as $i => $varianData) {
                $variantImage = null;

                // Cek apakah ada file gambar untuk varian ke-$i
                if (isset($variantFiles[$i]['image'])) {
                    $variantImage = $variantFiles[$i]['image']->store('product-variants', 'public');
                }

                $product->variants()->create([
                    'nama_varian' => $varianData['nama_varian'],
                    'harga'       => $varianData['harga'],
                    'stok'        => $varianData['stok'],
                    'image'       => $variantImage,
                ]);
            }
        });

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load('variants');
        return view('admin.products.create', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'              => 'required|exists:categories,id',
            'name'                     => 'required|string|max:255|unique:products,name,' . $product->id,
            'description'              => 'nullable|string',
            'price'                    => 'required|numeric|min:0',
            'stock'                    => 'required|integer|min:0',
            'image'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
            'is_popular'               => 'boolean',
            'is_new_arrival'           => 'boolean',
            'variants'                 => 'nullable|array',
            'variants.*.nama_varian'   => 'required_with:variants|string|max:100',
            'variants.*.harga'         => 'required_with:variants|numeric|min:0',
            'variants.*.stok'          => 'required_with:variants|integer|min:0',
            'variants.*.image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        DB::transaction(function () use ($request, $product) {
            $data = $request->only(['category_id', 'name', 'description', 'price', 'stock']);
            $data['is_popular']     = $request->boolean('is_popular');
            $data['is_new_arrival'] = $request->boolean('is_new_arrival');

            // Update gambar utama produk jika ada yang baru
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            // Hapus semua varian lama beserta gambarnya
            foreach ($product->variants as $oldVariant) {
                if ($oldVariant->image) {
                    Storage::disk('public')->delete($oldVariant->image);
                }
            }
            $product->variants()->delete();

            // Simpan varian baru beserta fotonya
            $variants     = $request->input('variants', []);
            $variantFiles = $request->file('variants', []);

            foreach ($variants as $i => $varianData) {
                $variantImage = null;

                if (isset($variantFiles[$i]['image'])) {
                    $variantImage = $variantFiles[$i]['image']->store('product-variants', 'public');
                }

                $product->variants()->create([
                    'nama_varian' => $varianData['nama_varian'],
                    'harga'       => $varianData['harga'],
                    'stok'        => $varianData['stok'],
                    'image'       => $variantImage,
                ]);
            }
        });

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar utama
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Hapus gambar semua varian
        foreach ($product->variants as $variant) {
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
        }

        $product->delete(); // cascade hapus varian via FK

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}
