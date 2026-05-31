<?php

namespace Database\Factories;

use App\Enums\FollowUpType;
use App\Models\FollowUp;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FollowUp>
 */
class FollowUpFactory extends Factory
{
    protected $model = FollowUp::class;

    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(FollowUpType::cases())->value,
            'scheduled_at' => fake()->dateTimeBetween('now', '+7 days'),
            'completed_at' => null,
            'is_done' => false,
            'outcome_note' => null,
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_done' => true,
            'completed_at' => now(),
            'outcome_note' => fake()->sentence(),
        ]);
    }
}
