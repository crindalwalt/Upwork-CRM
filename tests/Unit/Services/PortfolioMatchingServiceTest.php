<?php

use App\Enums\JobNiche;
use App\Models\Job;
use App\Models\Portfolio;
use App\Services\PortfolioMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns the featured matching portfolio as the best match', function () {
    $featured = Portfolio::factory()->create([
        'title' => 'Featured AI Agent Case Study',
        'tags' => ['ai_agent', 'automation'],
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    Portfolio::factory()->create([
        'tags' => ['ai_agent'],
        'is_featured' => false,
        'sort_order' => 5,
    ]);

    $job = Job::factory()->create([
        'niche' => JobNiche::AiAgent->value,
    ]);

    $match = app(PortfolioMatchingService::class)->findBestMatch($job);

    expect($match)->not->toBeNull()
        ->and($match?->is($featured))->toBeTrue();
});

it('returns only matching portfolios ordered by featured status', function () {
    $featured = Portfolio::factory()->create([
        'title' => 'Featured Match',
        'tags' => ['ai_agent'],
        'is_featured' => true,
        'sort_order' => 2,
    ]);

    $standard = Portfolio::factory()->create([
        'title' => 'Standard Match',
        'tags' => ['ai_agent', 'chatbot'],
        'is_featured' => false,
        'sort_order' => 1,
    ]);

    Portfolio::factory()->create([
        'title' => 'Unrelated Project',
        'tags' => ['web_scraping'],
        'is_featured' => true,
    ]);

    $job = Job::factory()->create([
        'niche' => JobNiche::AiAgent->value,
    ]);

    $matches = app(PortfolioMatchingService::class)->allMatches($job);

    expect($matches->modelKeys())->toBe([$featured->id, $standard->id]);
});
