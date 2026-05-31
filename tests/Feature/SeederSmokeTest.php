<?php

use App\Enums\UserRole;
use App\Models\Employer;
use App\Models\FollowUp;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Models\ProposalNote;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('seeds the required record counts and baseline admin data', function () {
    expect(User::query()->count())->toBe(4)
        ->and(User::query()->where('email', 'admin@crm.local')->first()?->role)->toBe(UserRole::Admin)
        ->and(Portfolio::query()->count())->toBe(5)
        ->and(Portfolio::query()->where('title', 'Altora AI Dialer')->exists())->toBeTrue()
        ->and(Portfolio::query()->where('is_featured', true)->count())->toBeGreaterThanOrEqual(2)
        ->and(Employer::query()->count())->toBe(20)
        ->and(Job::query()->count())->toBe(30)
        ->and(Proposal::query()->count())->toBe(15)
        ->and(Setting::query()->count())->toBe(6);
});

it('seeds notes and follow ups for the required proposal slices', function () {
    $proposals = Proposal::query()->withCount(['proposalNotes', 'followUps'])->get();

    expect(ProposalNote::query()->count())->toBeGreaterThanOrEqual(15)
        ->and(FollowUp::query()->count())->toBeGreaterThanOrEqual(11);

    foreach ($proposals as $proposal) {
        expect($proposal->proposal_notes_count)->toBeGreaterThanOrEqual(1)
            ->and($proposal->proposal_notes_count)->toBeLessThanOrEqual(2);

        if ($proposal->isActive()) {
            expect($proposal->follow_ups_count)->toBeGreaterThanOrEqual(1)
                ->and($proposal->follow_ups_count)->toBeLessThanOrEqual(2);
        }
    }
});
