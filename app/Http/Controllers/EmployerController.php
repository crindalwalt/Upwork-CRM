<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployerRequest;
use App\Models\Employer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Employer::class, 'employer');
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'flag' => $request->string('flag')->toString(),
            'verified' => $request->string('verified')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'newest',
        ];

        $query = Employer::query()->withCount(['jobs', 'proposals']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($employerQuery) use ($search): void {
                $employerQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('internal_notes', 'like', "%{$search}%");
            });
        }

        if ($filters['flag'] !== '') {
            $query->where('flag', $filters['flag']);
        }

        if ($filters['verified'] !== '') {
            $query->where('payment_verified', $filters['verified'] === '1');
        }

        match ($filters['sort']) {
            'highest_quality' => $query->orderByDesc('hire_rate')->orderByDesc('total_spent'),
            'most_jobs' => $query->orderByDesc('jobs_count')->orderByDesc('proposals_count'),
            default => $query->latest(),
        };

        return view('employers.index', [
            'employers' => $query->paginate(18)->withQueryString(),
            'filters' => $filters,
            'stats' => [
                'total' => Employer::query()->count(),
                'verified' => Employer::query()->verified()->count(),
                'high_quality' => Employer::query()->highQuality()->count(),
                'flagged' => Employer::query()->whereNotNull('flag')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('employers.create');
    }

    public function store(StoreEmployerRequest $request): RedirectResponse
    {
        $employer = Employer::query()->create($this->employerPayload($request));

        return redirect()->route('employers.show', $employer)->with('success', 'Employer created.');
    }

    public function show(Employer $employer): View
    {
        $employer->loadCount(['jobs', 'proposals']);

        return view('employers.show', [
            'employer' => $employer,
            'recentJobs' => $employer->jobs()->latest('posted_at')->take(6)->get(),
            'recentProposals' => $employer->proposals()->with('job')->latest()->take(6)->get(),
        ]);
    }

    public function edit(Employer $employer): View
    {
        return view('employers.edit', ['employer' => $employer]);
    }

    public function update(StoreEmployerRequest $request, Employer $employer): RedirectResponse
    {
        $employer->update($this->employerPayload($request));

        return redirect()->route('employers.show', $employer)->with('success', 'Employer updated.');
    }

    public function destroy(Employer $employer): RedirectResponse
    {
        if ($employer->jobs()->exists() || $employer->proposals()->exists()) {
            return back()->with('error', 'Employers linked to jobs or proposals cannot be deleted.');
        }

        $employer->delete();

        return redirect()->route('employers.index')->with('success', 'Employer deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function employerPayload(StoreEmployerRequest $request): array
    {
        $validated = $request->validated();
        $validated['payment_verified'] = (bool) ($validated['payment_verified'] ?? false);

        foreach (['upwork_url', 'location', 'total_spent', 'hire_rate', 'reviews_count', 'open_jobs_count', 'member_since', 'internal_notes', 'flag'] as $key) {
            $validated[$key] = $validated[$key] ?? null;
        }

        return $validated;
    }
}
