<?php

namespace App\Http\Controllers;

use App\Enums\FollowUpType;
use App\Enums\JobNiche;
use App\Enums\ProposalStatus;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Employer;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Services\JobScoringService;
use App\Services\PortfolioMatchingService;
use App\Services\ProposalService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function __construct(
        private readonly ProposalService $proposalService,
        private readonly PortfolioMatchingService $portfolioMatchingService,
        private readonly JobScoringService $jobScoringService,
        private readonly SettingsService $settingsService,
    ) {
        $this->authorizeResource(Proposal::class, 'proposal');
    }

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->string('status')->toString(),
            'niche' => $request->string('niche')->toString(),
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'search' => $request->string('search')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'created_at_desc',
        ];

        $query = Proposal::query()->with(['job', 'employer', 'user', 'leveragePortfolio']);

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['niche'] !== '') {
            $query->whereHas('job', fn ($jobQuery) => $jobQuery->where('niche', $filters['niche']));
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function ($proposalQuery) use ($search): void {
                $proposalQuery
                    ->where('cover_letter', 'like', "%{$search}%")
                    ->orWhereHas('job', fn ($jobQuery) => $jobQuery->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('employer', fn ($employerQuery) => $employerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        match ($filters['sort']) {
            'sent_at_desc' => $query->orderByDesc('sent_at'),
            'ai_score_desc' => $query->orderByDesc('ai_score')->orderByDesc('created_at'),
            'status_asc' => $query->orderBy('status')->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        return view('proposals.index', [
            'proposals' => $query->paginate(20)->withQueryString(),
            'stats' => $this->proposalService->getStats(auth()->user()),
            'statuses' => ProposalStatus::cases(),
            'niches' => JobNiche::cases(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('proposals.create', $this->proposalFormData());
    }

    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $proposal = $this->proposalService->create($request->validated(), $request->user());

        return redirect()
            ->route('proposals.show', $proposal)
            ->with('success', 'Proposal saved.');
    }

    public function show(Proposal $proposal): View
    {
        $proposal->load([
            'job.employer',
            'employer',
            'proposalNotes.user',
            'followUps.user',
            'leveragePortfolio',
            'user',
        ]);

        return view('proposals.show', [
            'proposal' => $proposal,
            'statuses' => ProposalStatus::cases(),
            'followUpTypes' => FollowUpType::cases(),
        ]);
    }

    public function edit(Proposal $proposal): View
    {
        return view('proposals.edit', $this->proposalFormData($proposal));
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        $validated = $request->validated();
        $status = isset($validated['status']) ? ProposalStatus::from($validated['status']) : null;

        $proposal->fill($this->proposalPayload($validated, $proposal));
        $proposal->save();

        if ($status !== null && $proposal->fresh()->status !== $status) {
            $this->proposalService->updateStatus($proposal->fresh(), $status);
        }

        return redirect()
            ->route('proposals.show', $proposal)
            ->with('success', 'Proposal updated.');
    }

    public function destroy(Proposal $proposal): RedirectResponse
    {
        $proposal->delete();

        return redirect()
            ->route('proposals.index')
            ->with('success', 'Proposal deleted.');
    }

    public function updateStatus(Request $request, Proposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_map(
                static fn (ProposalStatus $status) => $status->value,
                ProposalStatus::cases()
            ))],
        ]);

        $proposal = $this->proposalService->updateStatus($proposal, ProposalStatus::from($validated['status']));

        return response()->json([
            'success' => true,
            'status' => $proposal->status->value,
            'label' => $proposal->status->label(),
            'color' => $this->statusClasses($proposal->status),
        ]);
    }

    public function recordLoomView(Proposal $proposal): JsonResponse
    {
        $this->authorize('update', $proposal);

        $proposal = $this->proposalService->recordLoomView($proposal);

        return response()->json([
            'success' => true,
            'view_count' => $proposal->loom_view_count,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function proposalFormData(?Proposal $proposal = null): array
    {
        $jobs = Job::query()->with('employer')->orderByDesc('posted_at')->get();
        $portfolios = Portfolio::query()->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('title')->get();

        $jobScores = $jobs->mapWithKeys(fn (Job $job) => [
            $job->id => [
                'score' => $this->jobScoringService->score($job, $job->employer)['score'],
                'reasoning' => $this->jobScoringService->score($job, $job->employer)['reasoning'],
                'flags' => $this->jobScoringService->score($job, $job->employer)['flags'],
            ],
        ]);

        $portfolioMatches = $jobs->mapWithKeys(function (Job $job): array {
            $match = $this->portfolioMatchingService->findBestMatch($job);

            return [
                $job->id => $match ? [
                    'id' => $match->id,
                    'title' => $match->title,
                    'outcome_summary' => $match->outcome_summary,
                ] : null,
            ];
        });

        return [
            'proposal' => $proposal,
            'jobs' => $jobs,
            'employers' => Employer::query()->orderBy('name')->get(),
            'portfolios' => $portfolios,
            'statuses' => ProposalStatus::cases(),
            'niches' => JobNiche::cases(),
            'jobScoresJson' => $jobScores->toJson(),
            'portfolioMatchesJson' => $portfolioMatches->toJson(),
            'jobsJson' => $jobs->map(function (Job $job): array {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'employer_id' => $job->employer_id,
                    'employer_name' => $job->employer?->name,
                    'posted_at' => $job->posted_at?->toDateTimeString(),
                    'niche' => $job->niche?->label(),
                    'budget_type' => $job->budget_type?->value,
                    'budget_display' => $job->budgetDisplay(),
                    'proposals_count_at_time' => $job->proposals_count_at_time,
                    'employer' => [
                        'name' => $job->employer?->name,
                        'hire_rate' => $job->employer?->hire_rate,
                        'total_spent' => $job->employer?->total_spent,
                        'payment_verified' => $job->employer?->payment_verified,
                    ],
                ];
            })->toJson(),
            'settingsService' => $this->settingsService,
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function proposalPayload(array $validated, Proposal $proposal): array
    {
        if (array_key_exists('job_id', $validated)) {
            $job = Job::query()->findOrFail($validated['job_id']);
            $validated['employer_id'] = $validated['employer_id'] ?? $job->employer_id;
        }

        if (array_key_exists('has_leverage', $validated) && ! $validated['has_leverage']) {
            $validated['leverage_portfolio_id'] = null;
            $validated['leverage_notes'] = null;
        }

        unset($validated['status']);

        return $validated;
    }

    private function statusClasses(ProposalStatus $status): string
    {
        return match ($status) {
            ProposalStatus::Won => 'bg-emerald-100 text-emerald-800',
            ProposalStatus::Viewed => 'bg-amber-100 text-amber-800',
            ProposalStatus::Lost => 'bg-red-100 text-red-800',
            ProposalStatus::Sent => 'bg-blue-100 text-blue-800',
            ProposalStatus::Replied => 'bg-purple-100 text-purple-800',
            ProposalStatus::InterviewScheduled => 'bg-slate-100 text-slate-800',
            ProposalStatus::Withdrawn => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
