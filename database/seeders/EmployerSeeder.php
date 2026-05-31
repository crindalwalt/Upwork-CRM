<?php

namespace Database\Seeders;

use App\Models\Employer;
use Illuminate\Database\Seeder;

class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        Employer::factory()
            ->count(5)
            ->create([
                'payment_verified' => true,
                'hire_rate' => fake()->randomFloat(2, 70, 95),
                'flag' => 'green',
            ]);

        Employer::factory()
            ->count(15)
            ->create();
    }
}
