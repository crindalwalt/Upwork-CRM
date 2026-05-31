<?php

use App\Enums\BudgetType;
use App\Models\Employer;
use App\Models\Job;
use App\Services\JobScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('scores a high budget verified low competition recent job strongly', function () {
    $employer = Employer::factory()->create([
        'payment_verified' => true,
        'hire_rate' => 82,
        'total_spent' => 15000,
    ]);

    $job = Job::factory()->highBudget()->recent()->create([
        'employer_id' => $employer->id,
        'budget_type' => BudgetType::Fixed->value,
        'budget_max' => 3500,
        'proposals_count_at_time' => 4,
    ]);

    $result = app(JobScoringService::class)->score($job, $employer);

    expect($result['score'])->toBeGreaterThanOrEqual(8)
        ->and($result['reasoning'])->not->toBe('')
        ->and($result['flags'])->not->toContain('Unverified payment method');
});

it('scores a low budget unverified high competition job poorly', function () {
    $employer = Employer::factory()->create([
        'payment_verified' => false,
        'hire_rate' => 32,
        'total_spent' => 250,
    ]);

    $job = Job::factory()->create([
        'employer_id' => $employer->id,
        'budget_type' => BudgetType::Fixed->value,
        'budget_min' => 100,
        'budget_max' => 200,
        'posted_at' => now()->subDays(3),
        'proposals_count_at_time' => 35,
    ]);

    $result = app(JobScoringService::class)->score($job, $employer);

    expect($result['score'])->toBeLessThanOrEqual(4)
        ->and(implode(' | ', $result['flags']))->toContain('Unverified payment method')
        ->and(implode(' | ', $result['flags']))->toContain('High competition');
});
