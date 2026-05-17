<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_popular',
        'is_new_arrival'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_new_arrival' => 'boolean'
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Satu produk MEMILIKI BANYAK varian (one-to-many).
     * Contoh: "Pulpen Standard" → [Hitam, Biru, Merah]
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
