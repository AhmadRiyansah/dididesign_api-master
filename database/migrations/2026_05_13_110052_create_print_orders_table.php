<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();

            // Jenis layanan: spanduk | undangan | brosur | kartu_nama | cetak_file
            $table->string('service_type', 30);

            // File yang diupload (opsional, tergantung layanan)
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();

            // Spesifikasi cetak
            $table->string('paper_size', 20)->nullable();   // a4 | f4 | a3 | a5 | a6 | custom
            $table->string('ink_type', 10)->nullable();     // bw | color
            $table->string('binding', 20)->nullable();      // none | staples | spiral | soft_cover | hard_cover
            $table->integer('quantity')->default(1);
            $table->string('sides', 10)->nullable();        // single | double (untuk brosur)

            // Dimensi (khusus spanduk: panjang x lebar meter)
            $table->decimal('width_meter', 6, 2)->nullable();
            $table->decimal('height_meter', 6, 2)->nullable();

            // Harga
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('binding_cost', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            // Pembayaran & Status
            $table->string('payment_method', 20)->default('qris'); // qris | transfer
            $table->string('payment_status', 20)->default('pending'); // pending | paid | failed
            $table->string('order_status', 20)->default('process'); // process | printing | done | cancel

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_orders');
    }
};
