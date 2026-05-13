<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('order_code')->unique();
    $table->decimal('total_price', 12, 2);
    $table->decimal('shipping_cost', 12, 2)->default(0);
    $table->decimal('grand_total', 12, 2);

    $table->string('payment_method');

    $table->enum('payment_status', [
        'pending',
        'paid',
        'failed'
    ])->default('pending');

    $table->enum('order_status', [
        'process',
        'shipping',
        'done',
        'cancel'
    ])->default('process');

    $table->text('address');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
