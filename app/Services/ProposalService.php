<?php

namespace App\Services;

use App\Enums\ProposalStatus;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProposalService
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {
    }

    public function create(array $data, User $user): Proposal
    {
        $validated = Validator::make($data, [
            'job_id' => ['required', 'exists:jobs,id'],
            'employer_id' => ['nullable', 'exists:employers,id'],
            'connects_spent' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['nullable', Rule::in(array_map(
                static fn (ProposalStatus $status) => $status->value,
                ProposalStatus::cases()
            ))],
            'bid_amount' => ['nullable', 'numeric', 'min:0'],
            'bid_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'cover_letter' => ['nullable', 'string'],
            'loom_url' => ['nullable', 'url'],
            'has_leverage' => ['nullable', 'boolean'],
            'leverage_portfolio_id' => ['nullable', 'exists:portfolios,id'],
            'leverage_notes' => ['nullable', 'string'],
        ])->validate();

        $job = Job::query()->findOrFail($validated['job_id']);
        $status = ProposalStatus::from($validated['status'] ?? ProposalStatus::Draft->value);
        $hasLeverage = filter_var($validated['has_leverage'] ?? false, FILTER_VALIDATE_BOOL);

        $payload = [
            ...Arr::except($validated, ['status']),
            'status' => $status,
            'employer_id' => $validated['employer_id'] ?? $job->employer_id,
            'user_id' => $user->id,
            'has_leverage' => $hasLeverage,
        ];

        if (! $hasLeverage) {
            $payload['leverage_portfolio_id'] = null;
            $payload['leverage_notes'] = null;
        }

        $payload = [...$payload, ...$this->lifecycleAttributesFor($status)];

        return Proposal::query()->create($payload);
    }

    public function updateStatus(Proposal $proposal, ProposalStatus $status): Proposal
    {
        if ($status === ProposalStatus::Sent) {
            $proposal->markAsSent();

            return $proposal->fresh();
        }

        if ($status === ProposalStatus::Viewed) {
            $proposal->markAsViewed();

            return $proposal->fresh();
        }

        if ($status === ProposalStatus::Replied) {
            $proposal->markAsReplied();

            return $proposal->fresh();
        }

        $attributes = ['status' => $status];

        if ($status === ProposalStatus::InterviewScheduled) {
            $attributes['interview_at'] = $proposal->interview_at ?? now();
            $attributes['sent_at'] = $proposal->sent_at ?? now();
        }

        if (in_array($status, [ProposalStatus::Won, ProposalStatus::Lost, ProposalStatus::Withdrawn], true)) {
            $attributes['closed_at'] = $proposal->closed_at ?? now();
            $attributes['sent_at'] = $proposal->sent_at ?? now();
        }

        $proposal->fill($attributes)->save();

        return $proposal->fresh();
    }

    public function recordLoomView(Proposal $proposal): Proposal
    {
        $proposal->markAsViewed();

        return $proposal->fresh();
    }

    public function attachPortfolio(Proposal $proposal, Portfolio $portfolio, ?string $notes = null): Proposal
    {
        $proposal->fill([
            'has_leverage' => true,
            'leverage_portfolio_id' => $portfolio->id,
            'leverage_notes' => $notes,
        ])->save();

        return $proposal->fresh();
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(?User $user = null): array
    {
        $baseQuery = Proposal::query();

        if ($user !== null) {
            $baseQuery->where('user_id', $user->id);
        }

        $totalSent = (clone $baseQuery)->whereNotNull('sent_at')->count();
        $totalViewed = (clone $baseQuery)->where('status', ProposalStatus::Viewed->value)->count();
        $totalReplied = (clone $baseQuery)->where('status', ProposalStatus::Replied->value)->count();
        $totalWon = (clone $baseQuery)->where('status', ProposalStatus::Won->value)->count();
        $totalLost = (clone $baseQuery)->where('status', ProposalStatus::Lost->value)->count();
        $connectsSpentThisWeek = (clone $baseQuery)->thisWeek()->sum('connects_spent');
        $weeklyBudget = (int) $this->settingsService->get('weekly_connect_budget', (int) env('APP_CONNECTS_WEEKLY_BUDGET', 120));

        return [
            'total_sent' => $totalSent,
            'total_viewed' => $totalViewed,
            'total_replied' => $totalReplied,
            'total_won' => $totalWon,
            'total_lost' => $totalLost,
            'view_rate' => $this->rate($totalViewed, $totalSent),
            'reply_rate' => $this->rate($totalReplied, $totalSent),
            'win_rate' => $this->rate($totalWon, $totalSent),
            'connects_spent_this_week' => (int) $connectsSpentThisWeek,
            'connects_remaining' => max(0, $weeklyBudget - (int) $connectsSpentThisWeek),
        ];
    }

    private function rate(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    /**
     * @return array<string, mixed>
     */
    private function lifecycleAttributesFor(ProposalStatus $status): array
    {
        return match ($status) {
            ProposalStatus::Sent => ['sent_at' => now()],
            ProposalStatus::Viewed => [
                'sent_at' => now(),
                'loom_viewed' => true,
                'loom_viewed_at' => now(),
                'loom_view_count' => 1,
            ],
            ProposalStatus::Replied => [
                'sent_at' => now(),
                'replied_at' => now(),
            ],
            ProposalStatus::InterviewScheduled => [
                'sent_at' => now(),
                'interview_at' => now(),
            ],
            ProposalStatus::Won => [
                'sent_at' => now(),
                'closed_at' => now(),
            ],
            ProposalStatus::Lost, ProposalStatus::Withdrawn => [
                'sent_at' => now(),
                'closed_at' => now(),
            ],
            default => [],
        };
    }
}
