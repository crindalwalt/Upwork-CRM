<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $employerIds = Employer::query()->pluck('id')->all();

        Job::factory()
            ->count(10)
            ->recent()
            ->create([
                'employer_id' => fn () => fake()->randomElement($employerIds),
            ]);

        Job::factory()
            ->count(20)
            ->create([
                'employer_id' => fn () => fake()->randomElement($employerIds),
            ]);
    }
}
