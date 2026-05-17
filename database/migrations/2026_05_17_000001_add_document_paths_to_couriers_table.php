<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('ktp_path')->nullable()->after('plate_number');
            $table->string('vehicle_photo_path')->nullable()->after('ktp_path');
        });
    }

    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn(['ktp_path', 'vehicle_photo_path']);
        });
    }
};
