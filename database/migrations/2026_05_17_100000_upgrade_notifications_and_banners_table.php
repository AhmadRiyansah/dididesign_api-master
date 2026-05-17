<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Upgrade tabel banners
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('banners', 'subtitle')) {
                $table->string('subtitle')->nullable();
            }
            if (!Schema::hasColumn('banners', 'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn('banners', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('banners', 'color')) {
                $table->string('color')->default('#B71C1C'); // hex color
            }
            if (!Schema::hasColumn('banners', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('banners', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
        });

        // Upgrade tabel notifications
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('notifications', 'body')) {
                $table->text('body');
            }
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->default('system'); // system | promo | order
            }
            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable(); // data tambahan (order_id, dll)
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['title', 'subtitle', 'description', 'image', 'color', 'is_active', 'sort_order']);
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'title', 'body', 'type', 'is_read', 'data']);
        });
    }
};
