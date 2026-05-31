<?php

use App\Enums\ProposalStatus;
use App\Models\Employer;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\User;

it('auto fills the employer when creating a proposal from a job', function () {
    $user = User::factory()->create();
    $employer = Employer::factory()->create();
    $job = Job::factory()->create([
        'employer_id' => $employer->id,
    ]);

    $proposal = Proposal::create([
        'job_id' => $job->id,
        'user_id' => $user->id,
        'status' => ProposalStatus::Draft,
        'connects_spent' => 8,
    ]);

    expect($proposal->fresh()->employer_id)->toBe($employer->id);
});

it('marks a proposal as viewed and updates loom metadata', function () {
    $proposal = Proposal::factory()->sent()->create();

    $proposal->markAsViewed();

    $proposal->refresh();

    expect($proposal->status)->toBe(ProposalStatus::Viewed)
        ->and($proposal->loom_viewed)->toBeTrue()
        ->and($proposal->loom_view_count)->toBe(1)
        ->and($proposal->loom_viewed_at)->not->toBeNull();
});

it('returns only active proposals through the active scope', function () {
    $sent = Proposal::factory()->sent()->create();
    $viewed = Proposal::factory()->viewed()->create();
    $replied = Proposal::factory()->replied()->create();
    Proposal::factory()->won()->create();
    Proposal::factory()->lost()->create();

    $activeIds = Proposal::query()->active()->pluck('id');

    expect($activeIds)->toHaveCount(3)
        ->and($activeIds)->toContain($sent->id, $viewed->id, $replied->id);
});
