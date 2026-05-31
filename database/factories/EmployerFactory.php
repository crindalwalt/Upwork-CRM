<?php

namespace Database\Factories;

use App\Models\Employer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employer>
 */
class EmployerFactory extends Factory
{
    protected $model = Employer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'upwork_url' => 'https://www.upwork.com/companies/~'.fake()->bothify('??????????'),
            'location' => fake()->randomElement([
                'United States',
                'United Kingdom',
                'Australia',
                'Canada',
                'Germany',
            ]),
            'total_spent' => fake()->randomFloat(2, 0, 50000),
            'hire_rate' => fake()->randomFloat(2, 20, 95),
            'reviews_count' => fake()->numberBetween(0, 200),
            'payment_verified' => fake()->boolean(80),
            'open_jobs_count' => fake()->numberBetween(0, 25),
            'member_since' => fake()->dateTimeBetween('-10 years', '-1 month'),
            'internal_notes' => fake()->optional()->sentence(),
            'flag' => fake()->randomElement(['green', 'green', 'yellow', 'red', null]),
        ];
    }
}
