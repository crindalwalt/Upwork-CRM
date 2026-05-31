<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProposalNote>
 */
class ProposalNoteFactory extends Factory
{
    protected $model = ProposalNote::class;

    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'user_id' => User::factory(),
            'loom_script' => fake()->sentences(4, true),
            'talking_points' => fake()->sentences(fake()->numberBetween(3, 5)),
            'internal_note' => fake()->optional()->paragraph(),
        ];
    }
}
