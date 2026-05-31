<?php

use App\Enums\BudgetType;
use App\Enums\FollowUpType;
use App\Enums\JobDifficulty;
use App\Enums\JobNiche;
use App\Enums\ProposalStatus;
use App\Enums\UserRole;

it('defines the expected number of cases for each enum', function () {
    expect(ProposalStatus::cases())->toHaveCount(8)
        ->and(BudgetType::cases())->toHaveCount(2)
        ->and(JobNiche::cases())->toHaveCount(9)
        ->and(FollowUpType::cases())->toHaveCount(4)
        ->and(UserRole::cases())->toHaveCount(2)
        ->and(JobDifficulty::cases())->toHaveCount(3);
});

it('returns non empty labels for enums that expose labels', function () {
    foreach (ProposalStatus::cases() as $status) {
        expect($status->label())->not->toBe('');
    }

    foreach (JobNiche::cases() as $niche) {
        expect($niche->label())->not->toBe('');
    }
});

it('returns non empty colors for proposal statuses', function () {
    foreach (ProposalStatus::cases() as $status) {
        expect($status->color())->not->toBe('');
    }
});

it('returns the expected active proposal statuses', function () {
    expect(ProposalStatus::activeStatuses())
        ->toHaveCount(4)
        ->toBe([
            ProposalStatus::Sent,
            ProposalStatus::Viewed,
            ProposalStatus::Replied,
            ProposalStatus::InterviewScheduled,
        ]);
});
