<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Courier extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_type',
        'plate_number',
        'ktp_path',
        'vehicle_photo_path',
        'is_available',
        'current_lat',
        'current_lng',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'current_lat' => 'decimal:7',
            'current_lng' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
