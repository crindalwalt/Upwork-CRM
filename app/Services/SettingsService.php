<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\Setting;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $type = match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_array($value) => 'json',
            default => 'string',
        };

        Setting::set($key, $value, $type);
    }

    public function getConnectsRemainingThisWeek(): int
    {
        $budget = (int) $this->get('weekly_connect_budget', (int) env('APP_CONNECTS_WEEKLY_BUDGET', 120));
        $spent = (int) Proposal::query()->thisWeek()->sum('connects_spent');

        return max(0, $budget - $spent);
    }

    public function getDailyProposalTarget(): int
    {
        return (int) $this->get('daily_proposal_target', 3);
    }

    public function getMinAiScore(): int
    {
        return (int) $this->get('min_ai_score_to_propose', 7);
    }
}
