<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('openai_api_key', '', 'string', 'OpenAI API key');
        Setting::set('openai_model', 'gpt-4o', 'string', 'OpenAI model to use');
        Setting::set('weekly_connect_budget', '120', 'integer', 'Max connects to spend per week');
        Setting::set('daily_proposal_target', '3', 'integer', 'Target proposals per day');
        Setting::set('min_ai_score_to_propose', '7', 'integer', 'Minimum AI score before proposing');
        Setting::set('app_timezone', 'Asia/Karachi', 'string', 'User local timezone');
    }
}
