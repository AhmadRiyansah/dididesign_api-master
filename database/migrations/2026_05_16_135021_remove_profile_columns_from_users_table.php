<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone', 'photo']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('photo')->nullable()->after('password');
        });

        if (Schema::hasTable('profiles')) {
            DB::table('profiles')->orderBy('id')->chunkById(100, function ($profiles) {
                foreach ($profiles as $profile) {
                    DB::table('users')->where('id', $profile->user_id)->update([
                        'name'  => $profile->name,
                        'phone' => $profile->phone,
                        'photo' => $profile->photo,
                    ]);
                }
            });
        }
    }
};
