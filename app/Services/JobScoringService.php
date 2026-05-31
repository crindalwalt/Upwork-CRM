<?php

namespace App\Services;

use App\Enums\BudgetType;
use App\Models\Employer;
use App\Models\Job;
use ArrayObject;

class JobScoringService
{
    /**
     * @return ArrayObject<string, int|string|array<int, string>>
     */
    public function score(Job $job, ?Employer $employer = null): ArrayObject
    {
        $employer ??= $job->employer;
        $score = 2;
        $reasons = [];
        $flags = [];

        if ($job->budget_type === BudgetType::Fixed) {
            $budgetMax = $job->budget_max === null ? 0.0 : (float) $job->budget_max;

            if ($budgetMax >= 1000) {
                $score += 3;
                $reasons[] = 'Strong fixed budget';
            } elseif ($budgetMax >= 500) {
                $score += 2;
                $reasons[] = 'Acceptable fixed budget';
            } else {
                $flags[] = 'Budget below preferred fixed-project threshold';
            }
        } else {
            $hourlyMax = $job->hourly_rate_max === null ? 0.0 : (float) $job->hourly_rate_max;

            if ($hourlyMax >= 60) {
                $score += 1;
                $reasons[] = 'Healthy hourly ceiling';
            } else {
                $flags[] = 'Hourly ceiling is below target';
            }
        }

        if ($employer !== null) {
            if ($employer->payment_verified) {
                $score += 1;
                $reasons[] = 'Verified payment method';
            } else {
                $flags[] = 'Unverified payment method';
            }

            if ($employer->hire_rate !== null && (float) $employer->hire_rate >= 60) {
                $score += 1;
                $reasons[] = 'Healthy client hire rate';
            } elseif ($employer->hire_rate !== null) {
                $flags[] = sprintf('Low hire rate (%.2f%%)', (float) $employer->hire_rate);
            }

            if ($employer->total_spent !== null && (float) $employer->total_spent >= 1000) {
                $score += 1;
                $reasons[] = 'Client has meaningful spend history';
            }
        }

        $proposalCount = $job->proposals_count_at_time ?? 0;

        if ($proposalCount > 20) {
            $score -= 2;
            $flags[] = sprintf('High competition (%d proposals)', $proposalCount);
        } elseif ($proposalCount > 10) {
            $score -= 1;
            $flags[] = sprintf('Moderate competition (%d proposals)', $proposalCount);
        } else {
            $reasons[] = 'Competition is manageable';
        }

        if ($job->posted_at !== null && $job->posted_at->greaterThanOrEqualTo(now()->subDay())) {
            $score += 1;
            $reasons[] = 'Recently posted job';
        }

        $score = max(1, min(10, $score));

        return new ArrayObject([
            'score' => $score,
            'reasoning' => implode('; ', $reasons) ?: 'Limited signal available for this job.',
            'flags' => $flags,
        ]);
    }
}
