<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan varian dari setiap produk.
     * Contoh: Produk "Pulpen Standard" → varian Hitam, Biru, Merah
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // hapus varian jika produk dihapus
            $table->string('nama_varian');       // contoh: Hitam, Biru, Merah
            $table->decimal('harga', 12, 2);     // harga spesifik varian ini
            $table->integer('stok')->default(0); // stok per varian
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
