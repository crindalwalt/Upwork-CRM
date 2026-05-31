<?php

use App\Enums\FollowUpType;
use App\Models\Employer;
use App\Models\FollowUp;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Models\User;

test('authenticated users can render the new crm pages', function () {
    $user = User::factory()->create();
    $employer = Employer::factory()->create();
    $job = Job::factory()->create([
        'employer_id' => $employer->id,
    ]);
    $portfolio = Portfolio::factory()->create();
    $proposal = Proposal::factory()->create([
        'job_id' => $job->id,
        'employer_id' => $employer->id,
        'user_id' => $user->id,
        'leverage_portfolio_id' => $portfolio->id,
    ]);
    $followUp = FollowUp::factory()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $user->id,
    ]);

    $paths = [
        '/',
        '/profile',
        '/proposals',
        '/proposals/create',
        "/proposals/{$proposal->id}",
        "/proposals/{$proposal->id}/edit",
        '/jobs',
        '/jobs/create',
        "/jobs/{$job->id}",
        "/jobs/{$job->id}/edit",
        '/employers',
        '/employers/create',
        "/employers/{$employer->id}",
        "/employers/{$employer->id}/edit",
        '/portfolio',
        '/portfolio/create',
        "/portfolio/{$portfolio->id}",
        "/portfolio/{$portfolio->id}/edit",
        '/follow-ups',
        '/follow-ups/create',
        "/follow-ups/{$followUp->id}/edit",
        '/ai-tools',
    ];

    foreach ($paths as $path) {
        $this->actingAs($user)
            ->get($path)
            ->assertOk();
    }
});

test('authenticated users can create and complete a follow up', function () {
    $user = User::factory()->create();
    $proposal = Proposal::factory()->create([
        'user_id' => $user->id,
    ]);

    $storeResponse = $this->actingAs($user)
        ->post('/follow-ups', [
            'proposal_id' => $proposal->id,
            'type' => FollowUpType::Message->value,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'outcome_note' => 'Initial reminder',
        ]);

    $storeResponse->assertRedirect(route('proposals.show', $proposal));

    $followUp = FollowUp::query()->latest()->first();

    expect($followUp)
        ->not->toBeNull()
        ->proposal_id->toBe($proposal->id)
        ->user_id->toBe($user->id)
        ->is_done->toBeFalse();

    $this->actingAs($user)
        ->patch(route('follow-ups.complete', $followUp), [
            'outcome_note' => 'Completed after reply landed',
        ])
        ->assertRedirect();

    expect($followUp->fresh())
        ->is_done->toBeTrue()
        ->outcome_note->toBe('Completed after reply landed')
        ->completed_at->not->toBeNull();
});
