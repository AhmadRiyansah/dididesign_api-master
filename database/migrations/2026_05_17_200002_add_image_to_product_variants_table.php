<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom 'image' ke tabel product_variants
 * agar setiap varian bisa punya foto sendiri.
 * Contoh: Varian "Merah" punya foto pulpen merah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('image')->nullable()->after('stok');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
