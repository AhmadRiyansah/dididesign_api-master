<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'kurir') NOT NULL DEFAULT 'user'");
        } else {
            // SQLite / testing: role is stored as string-compatible value
            DB::table('users')->whereNotIn('role', ['admin', 'user', 'kurir'])->update(['role' => 'user']);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'kurir')->update(['role' => 'user']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
        }
    }
};
