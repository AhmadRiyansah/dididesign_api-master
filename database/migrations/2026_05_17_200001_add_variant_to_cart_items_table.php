<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah kolom product_variant_id ke cart_items.
 * 
 * Ini memungkinkan keranjang menyimpan varian yang dipilih user.
 * Contoh: User pilih "Pulpen Standard" warna Biru → simpan product_variant_id=2
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Tambah setelah product_id, nullable agar cart lama tidak rusak
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
