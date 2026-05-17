<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        if (Schema::hasColumn('users', 'name')) {
            $now = now();

            DB::table('users')->orderBy('id')->chunkById(100, function ($users) use ($now) {
                foreach ($users as $user) {
                    DB::table('profiles')->insert([
                        'user_id'    => $user->id,
                        'name'       => $user->name,
                        'phone'      => $user->phone,
                        'photo'      => $user->photo,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
