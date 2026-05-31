<?php

namespace Database\Seeders;

use App\Models\FollowUp;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Models\ProposalNote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProposalSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = Job::query()->with('employer')->get();
        $users = User::query()->get();
        $portfolios = Portfolio::query()->get();

        $this->seedStatusGroup(5, 'sent', $jobs, $users, $portfolios);
        $this->seedStatusGroup(3, 'viewed', $jobs, $users, $portfolios);
        $this->seedStatusGroup(3, 'replied', $jobs, $users, $portfolios);
        $this->seedStatusGroup(2, 'won', $jobs, $users, $portfolios);
        $this->seedStatusGroup(2, 'lost', $jobs, $users, $portfolios);
    }

    private function seedStatusGroup(
        int $count,
        string $state,
        Collection $jobs,
        Collection $users,
        Collection $portfolios,
    ): void {
        for ($index = 0; $index < $count; $index++) {
            /** @var Job $job */
            $job = $jobs->random();
            /** @var User $user */
            $user = $users->random();
            $hasLeverage = fake()->boolean(60);
            $portfolio = $hasLeverage ? $portfolios->random() : null;

            $proposal = Proposal::factory()
                ->{$state}()
                ->create([
                    'job_id' => $job->id,
                    'employer_id' => $job->employer_id,
                    'user_id' => $user->id,
                    'has_leverage' => $hasLeverage,
                    'leverage_portfolio_id' => $portfolio?->id,
                    'leverage_notes' => $portfolio ? fake()->sentence() : null,
                ]);

            ProposalNote::factory()
                ->count(fake()->numberBetween(1, 2))
                ->create([
                    'proposal_id' => $proposal->id,
                    'user_id' => $user->id,
                ]);

            if ($proposal->isActive()) {
                FollowUp::factory()
                    ->count(fake()->numberBetween(1, 2))
                    ->create([
                        'proposal_id' => $proposal->id,
                        'user_id' => $user->id,
                    ]);
            }
        }
    }
}
