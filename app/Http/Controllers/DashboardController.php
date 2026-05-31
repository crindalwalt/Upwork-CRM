<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Job;
use App\Models\Proposal;
use App\Services\ProposalService;
use App\Services\SettingsService;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProposalService $proposalService,
        private readonly SettingsService $settingsService,
    ) {
    }

    public function index(): View
    {
        if (! auth()->check()) {
            return view('auth.login');
        }

        $user = auth()->user();
        $stats = $this->proposalService->getStats($user);
        $recentProposals = Proposal::query()
            ->with(['job', 'employer'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(7)
            ->get();

        $todayFollowUps = FollowUp::query()
            ->with('proposal.job')
            ->where('user_id', $user->id)
            ->dueToday()
            ->pending()
            ->orderBy('scheduled_at')
            ->get();

        $weeklyProposals = Proposal::query()
            ->where('user_id', $user->id)
            ->thisWeek()
            ->count();

        $topScoredJobs = Job::query()
            ->with(['employer'])
            ->withMax(['proposals as max_ai_score' => fn ($query) => $query->where('ai_score', '>=', 7)], 'ai_score')
            ->whereHas('proposals', fn ($query) => $query->where('ai_score', '>=', 7))
            ->latest()
            ->take(5)
            ->get();

        $chartPeriod = collect(CarbonPeriod::create(now()->subDays(13)->startOfDay(), '1 day', now()->startOfDay()));
        $counts = Proposal::query()
            ->where('user_id', $user->id)
            ->whereNotNull('sent_at')
            ->whereBetween('sent_at', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->get()
            ->groupBy(fn (Proposal $proposal) => $proposal->sent_at?->toDateString());

        $chartData = [
            'labels' => $chartPeriod->map(fn (Carbon $date) => $date->format('M j'))->all(),
            'data' => $chartPeriod->map(fn (Carbon $date) => $counts->get($date->toDateString(), collect())->count())->all(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'recentProposals' => $recentProposals,
            'todayFollowUps' => $todayFollowUps,
            'weeklyProposals' => $weeklyProposals,
            'topScoredJobs' => $topScoredJobs,
            'chartData' => $chartData,
            'dashboardConnectsRemaining' => $this->settingsService->getConnectsRemainingThisWeek(),
        ]);
    }
}
