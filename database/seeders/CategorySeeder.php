<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua kategori lama (gunakan delete agar FK tidak error)
        \App\Models\Category::query()->delete();

        $categories = [
            ['name' => 'ATK'],
            ['name' => 'Cetak & Percetakan'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create(['name' => $cat['name']]);
        }
    }
}
