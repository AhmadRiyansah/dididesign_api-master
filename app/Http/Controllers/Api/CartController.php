<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Ambil keranjang user beserta items, produk, dan varian.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load(['items.product.category', 'items.variant']);

        $items = $cart->items->map(function (CartItem $item) {
            $product = $item->product;
            $variant = $item->variant;

            return [
                'id'         => $item->id,
                'quantity'   => $item->quantity,
                'subtotal'   => $item->subtotal,
                'product'    => $product ? [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'price'         => $product->price,
                    'stock'         => $product->stock,
                    'image'         => $product->image,
                    'category_name' => $product->category?->name,
                ] : null,
                // Varian yang dipilih (null jika produk tanpa varian)
                'variant'    => $variant ? [
                    'id'          => $variant->id,
                    'nama_varian' => $variant->nama_varian,
                    'harga'       => $variant->harga,
                    'stok'        => $variant->stok,
                ] : null,
            ];
        });

        return response()->json([
            'cart_id' => $cart->id,
            'items'   => $items,
            'total'   => $items->sum('subtotal'),
        ]);
    }

    /**
     * Tambah item ke keranjang.
     *
     * Body JSON:
     * {
     *   "product_id": 1,
     *   "product_variant_id": 2,   ← opsional, jika produk punya varian
     *   "quantity": 3
     * }
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id'         => 'required|integer|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Jika menggunakan varian, cek stok dari varian
        $variantId = $validated['product_variant_id'] ?? null;

        if ($variantId) {
            // Pastikan varian milik produk yang benar
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->id)
                ->firstOrFail();

            if ($variant->stok < $validated['quantity']) {
                return response()->json(['message' => 'Stok varian tidak mencukupi.'], 422);
            }

            // Harga dari varian
            $harga = $variant->harga;
        } else {
            // Produk tanpa varian — cek stok produk
            if ($product->stock < $validated['quantity']) {
                return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
            }
            $harga = $product->price;
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // Cek apakah item (produk + varian yang sama) sudah ada di keranjang
        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $validated['quantity'];

            // Validasi stok ulang dengan total qty
            $stokTersedia = $variantId ? $variant->stok : $product->stock;
            if ($stokTersedia < $newQty) {
                return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
            }

            $existingItem->update([
                'quantity' => $newQty,
                'subtotal' => $harga * $newQty,
            ]);
            $item = $existingItem;
        } else {
            $item = $cart->items()->create([
                'product_id'         => $product->id,
                'product_variant_id' => $variantId,
                'quantity'           => $validated['quantity'],
                'subtotal'           => $harga * $validated['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Produk ditambahkan ke keranjang.',
            'item'    => $item->load('product', 'variant'),
        ], 201);
    }

    /**
     * Update quantity item keranjang.
     * Body: { quantity }
     */
    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->findOrFail($itemId);

        // Gunakan harga dari varian jika ada, jika tidak dari produk
        if ($item->variant) {
            $stok  = $item->variant->stok;
            $harga = $item->variant->harga;
        } else {
            $stok  = $item->product->stock;
            $harga = $item->product->price;
        }

        if ($stok < $validated['quantity']) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        $item->update([
            'quantity' => $validated['quantity'],
            'subtotal' => $harga * $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Keranjang diperbarui.',
            'item'    => $item->load('product', 'variant'),
        ]);
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang.']);
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Keranjang dikosongkan.']);
    }
}
