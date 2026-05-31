<?php

namespace Database\Factories;

use App\Enums\ProposalStatus;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        $sentAt = fake()->dateTimeBetween('-5 days', 'now');

        return [
            'job_id' => Job::factory(),
            'employer_id' => null,
            'user_id' => User::factory(),
            'status' => ProposalStatus::Sent->value,
            'connects_spent' => fake()->randomElement([6, 8, 10, 12, 16]),
            'bid_amount' => fake()->randomFloat(2, 300, 2000),
            'bid_hourly_rate' => null,
            'cover_letter' => fake()->paragraphs(2, true),
            'loom_url' => fake()->optional()->url(),
            'loom_view_count' => 0,
            'loom_viewed_at' => null,
            'loom_viewed' => false,
            'has_leverage' => fake()->boolean(60),
            'leverage_portfolio_id' => null,
            'leverage_notes' => fake()->optional()->sentence(),
            'ai_score' => fake()->numberBetween(5, 10),
            'ai_score_reasoning' => fake()->optional()->sentence(12),
            'ai_script' => fake()->optional()->paragraphs(2, true),
            'sent_at' => $sentAt,
            'replied_at' => null,
            'interview_at' => null,
            'closed_at' => null,
            'won_amount' => null,
            'loss_reason' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Sent->value,
            'sent_at' => now(),
            'replied_at' => null,
            'closed_at' => null,
        ]);
    }

    public function viewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Viewed->value,
            'sent_at' => now()->subDay(),
            'loom_view_count' => fake()->numberBetween(1, 5),
            'loom_viewed' => true,
            'loom_viewed_at' => now(),
        ]);
    }

    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Replied->value,
            'sent_at' => now()->subDays(2),
            'replied_at' => now(),
        ]);
    }

    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Won->value,
            'sent_at' => now()->subDays(4),
            'replied_at' => now()->subDays(2),
            'closed_at' => now(),
            'won_amount' => fake()->randomFloat(2, 1000, 10000),
            'loss_reason' => null,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Lost->value,
            'sent_at' => now()->subDays(4),
            'replied_at' => now()->subDays(1),
            'closed_at' => now(),
            'won_amount' => null,
            'loss_reason' => fake()->sentence(),
        ]);
    }
}
