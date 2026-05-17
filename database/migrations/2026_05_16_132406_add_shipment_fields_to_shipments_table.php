<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number')->nullable()->unique();
            $table->enum('status', [
                'pending',
                'picked_up',
                'in_transit',
                'delivered',
                'failed',
            ])->default('pending');
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropConstrainedForeignId('courier_id');
            $table->dropColumn([
                'tracking_number',
                'status',
                'picked_up_at',
                'delivered_at',
                'notes',
            ]);
        });
    }
};
