<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Shahzad Farooq',
            'email' => 'admin@crm.local',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        User::factory()
            ->count(3)
            ->create([
                'role' => UserRole::Intern,
                'is_active' => true,
            ]);
    }
}
