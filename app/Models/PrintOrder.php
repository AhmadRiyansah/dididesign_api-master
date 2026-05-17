<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintOrder extends Model
{
    protected $fillable = [
        'user_id', 'order_code', 'service_type',
        'file_path', 'file_name',
        'paper_size', 'ink_type', 'binding', 'quantity', 'sides',
        'width_meter', 'height_meter',
        'unit_price', 'binding_cost', 'total_price',
        'payment_method', 'payment_status', 'order_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price'   => 'decimal:2',
            'binding_cost' => 'decimal:2',
            'total_price'  => 'decimal:2',
            'width_meter'  => 'decimal:2',
            'height_meter' => 'decimal:2',
            'quantity'     => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Label ramah pengguna
    public function getStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'process'  => 'Diproses',
            'printing' => 'Sedang Dicetak',
            'done'     => 'Selesai',
            'cancel'   => 'Dibatalkan',
            default    => $this->order_status,
        };
    }

    public function getServiceLabelAttribute(): string
    {
        return match ($this->service_type) {
            'spanduk'     => 'Spanduk (Banner)',
            'undangan'    => 'Undangan',
            'brosur'      => 'Brosur',
            'kartu_nama'  => 'Kartu Nama',
            'cetak_file'  => 'Cetak File',
            default       => $this->service_type,
        };
    }
}
