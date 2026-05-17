<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@dididesign.com'],
            [
                'password' => bcrypt('password'),
                'role'     => UserRole::Admin,
            ]
        );

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'  => 'Admin',
                'phone' => null,
                'photo' => null,
            ]
        );
    }
}
