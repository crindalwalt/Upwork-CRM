<?php

namespace App\Services;

use App\Enums\JobNiche;
use App\Models\Job;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Collection;

class PortfolioMatchingService
{
    public function findBestMatch(Job $job): ?Portfolio
    {
        return $this->allMatches($job)->first();
    }

    public function allMatches(Job $job): Collection
    {
        $tag = $this->tagFor($job->niche instanceof JobNiche ? $job->niche : JobNiche::from($job->niche));

        return Portfolio::query()
            ->whereJsonContains('tags', $tag)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
    }

    private function tagFor(JobNiche $niche): string
    {
        return $niche->value;
    }
}
