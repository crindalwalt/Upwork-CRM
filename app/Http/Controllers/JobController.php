<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\JobDifficulty;
use App\Enums\JobNiche;
use App\Http\Requests\StoreJobRequest;
use App\Models\Employer;
use App\Models\Job;
use App\Services\JobScoringService;
use App\Services\PortfolioMatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(
        private readonly JobScoringService $jobScoringService,
        private readonly PortfolioMatchingService $portfolioMatchingService,
    ) {
        $this->authorizeResource(Job::class, 'job');
    }

    public function index(Request $request): View
    {
        $filters = [
            'niche' => $request->string('niche')->toString(),
            'budget_type' => $request->string('budget_type')->toString(),
            'search' => $request->string('search')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'newest',
        ];

        $query = Job::query()->with('employer')->withCount('proposals');

        if ($filters['niche'] !== '') {
            $query->where('niche', $filters['niche']);
        }

        if ($filters['budget_type'] !== '') {
            $query->where('budget_type', $filters['budget_type']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($jobQuery) use ($search): void {
                $jobQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employer', fn ($employerQuery) => $employerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        match ($filters['sort']) {
            'highest_budget' => $query->orderByDesc('budget_max')->orderByDesc('hourly_rate_max'),
            'most_proposals' => $query->orderByDesc('proposals_count')->orderByDesc('posted_at'),
            default => $query->latest(),
        };

        $jobs = $query->paginate(20)->withQueryString();
        $scores = $jobs->getCollection()->mapWithKeys(function (Job $job): array {
            $score = $this->jobScoringService->score($job, $job->employer);

            return [$job->id => $score['score']];
        });

        return view('jobs.index', [
            'jobs' => $jobs,
            'jobScores' => $scores,
            'niches' => JobNiche::cases(),
            'budgetTypes' => BudgetType::cases(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('jobs.create', $this->formData());
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $job = Job::query()->create($this->jobPayload($request));

        return redirect()->route('jobs.show', $job)->with('success', 'Job created.');
    }

    public function show(Job $job): View
    {
        $job->load(['employer', 'proposals.user']);

        return view('jobs.show', [
            'job' => $job,
            'score' => $this->jobScoringService->score($job, $job->employer)->getArrayCopy(),
            'portfolioMatch' => $this->portfolioMatchingService->findBestMatch($job),
            'proposals' => $job->proposals,
        ]);
    }

    public function edit(Job $job): View
    {
        return view('jobs.edit', $this->formData($job));
    }

    public function update(StoreJobRequest $request, Job $job): RedirectResponse
    {
        $job->update($this->jobPayload($request));

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        if ($job->proposals()->exists()) {
            return back()->with('error', 'Jobs with proposals cannot be deleted.');
        }

        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted.');
    }

    public function score(Job $job): JsonResponse
    {
        $this->authorize('view', $job);

        return response()->json($this->jobScoringService->score($job, $job->employer)->getArrayCopy());
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Job $job = null): array
    {
        return [
            'job' => $job,
            'employers' => Employer::query()->orderBy('name')->get(),
            'niches' => JobNiche::cases(),
            'budgetTypes' => BudgetType::cases(),
            'difficulties' => JobDifficulty::cases(),
            'existingUrlsJson' => Job::query()
                ->when($job, fn ($query) => $query->whereKeyNot($job->id))
                ->pluck('url')
                ->values()
                ->toJson(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function jobPayload(StoreJobRequest $request): array
    {
        $validated = $request->validated();
        $validated['required_skills'] = collect(explode(',', $request->input('required_skills_text', '')))
            ->map(static fn (string $skill) => trim($skill))
            ->filter()
            ->values()
            ->all();
        $validated['required_skills'] = $validated['required_skills'] === [] ? null : $validated['required_skills'];
        $validated['difficulty'] = $validated['difficulty'] ?? null;
        $validated['employer_id'] = $validated['employer_id'] ?? null;

        if (($validated['budget_type'] ?? null) === BudgetType::Fixed->value) {
            $validated['hourly_rate_min'] = null;
            $validated['hourly_rate_max'] = null;
        } else {
            $validated['budget_min'] = null;
            $validated['budget_max'] = null;
        }

        unset($validated['required_skills_text']);

        return $validated;
    }
}
