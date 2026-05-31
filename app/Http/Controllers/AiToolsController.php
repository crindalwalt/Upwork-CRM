<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use App\Services\JobScoringService;
use App\Services\PortfolioMatchingService;
use App\Services\SettingsService;
use Illuminate\View\View;

class AiToolsController extends Controller
{
    public function __construct(
        private readonly JobScoringService $jobScoringService,
        private readonly PortfolioMatchingService $portfolioMatchingService,
        private readonly SettingsService $settingsService,
    ) {
    }

    public function index(): View
    {
        $jobs = Job::query()->with('employer')->latest('posted_at')->take(10)->get();
        $scoredJobs = $jobs->map(function (Job $job): array {
            $score = $this->jobScoringService->score($job, $job->employer)->getArrayCopy();
            $match = $this->portfolioMatchingService->findBestMatch($job);

            return [
                'job' => $job,
                'score' => $score,
                'match' => $match,
            ];
        });

        return view('ai-tools.index', [
            'scoredJobs' => $scoredJobs,
            'lowScoreProposals' => Proposal::query()
                ->with(['job', 'employer'])
                ->whereNotNull('ai_score')
                ->where('ai_score', '<', $this->settingsService->getMinAiScore())
                ->latest()
                ->take(8)
                ->get(),
            'minAiScore' => $this->settingsService->getMinAiScore(),
            'dailyProposalTarget' => $this->settingsService->getDailyProposalTarget(),
            'connectsRemaining' => $this->settingsService->getConnectsRemainingThisWeek(),
            'openAiModel' => $this->settingsService->get('openai_model', config('services.openai.model')),
            'openAiKeyConfigured' => filled($this->settingsService->get('openai_api_key', config('services.openai.key'))),
        ]);
    }
}
