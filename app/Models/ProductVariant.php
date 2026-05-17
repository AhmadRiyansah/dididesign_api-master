<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'nama_varian',
        'harga',
        'stok',
        'image',  // foto khusus per varian (opsional)
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok'  => 'integer',
    ];

    /**
     * Setiap varian DIMILIKI oleh satu produk (many-to-one).
     * Contoh: Varian "Hitam" → dimiliki oleh Produk "Pulpen Standard"
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
