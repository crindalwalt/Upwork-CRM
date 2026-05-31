<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        Portfolio::query()->create([
            'title' => 'Altora AI Dialer',
            'description' => 'AI voice outreach platform with qualification logic, lead routing, and CRM synchronization.',
            'loom_url' => 'https://www.loom.com/share/altora-ai-dialer',
            'live_url' => null,
            'github_url' => null,
            'tags' => ['voice_ai', 'ai_agent', 'automation'],
            'tech_stack' => ['Python', 'FastAPI', 'OpenAI API', 'Twilio'],
            'client_name' => 'Altora (Australia)',
            'client_location' => 'Australia',
            'outcome_summary' => 'Reduced manual outbound call time by 80% using AI voice agent',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Portfolio::factory()
            ->count(2)
            ->create(['is_featured' => true]);

        Portfolio::factory()
            ->count(2)
            ->create();
    }
}
