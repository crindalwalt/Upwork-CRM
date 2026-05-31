<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortfolioRequest;
use App\Http\Requests\UpdatePortfolioRequest;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Portfolio::class, 'portfolio');
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'tag' => $request->string('tag')->toString(),
            'featured' => $request->string('featured')->toString(),
            'sort' => $request->string('sort')->toString() ?: 'manual',
        ];

        $query = Portfolio::query()->withCount('proposals');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($portfolioQuery) use ($search): void {
                $portfolioQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('outcome_summary', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        if ($filters['tag'] !== '') {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        if ($filters['featured'] !== '') {
            $query->where('is_featured', $filters['featured'] === '1');
        }

        match ($filters['sort']) {
            'recent' => $query->latest(),
            'most_used' => $query->orderByDesc('proposals_count')->orderBy('sort_order'),
            default => $query->orderBy('sort_order')->orderByDesc('is_featured')->orderBy('title'),
        };

        return view('portfolio.index', [
            'portfolioItems' => $query->paginate(18)->withQueryString(),
            'filters' => $filters,
            'tags' => Portfolio::query()->pluck('tags')->flatten()->filter()->unique()->sort()->values(),
            'stats' => [
                'total' => Portfolio::query()->count(),
                'featured' => Portfolio::query()->featured()->count(),
                'with_video' => Portfolio::query()->whereNotNull('loom_url')->count(),
                'used' => Portfolio::query()->has('proposals')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('portfolio.create');
    }

    public function store(StorePortfolioRequest $request): RedirectResponse
    {
        $portfolio = Portfolio::query()->create($this->portfolioPayload($request));

        return redirect()->route('portfolio.show', $portfolio)->with('success', 'Portfolio piece created.');
    }

    public function show(Portfolio $portfolio): View
    {
        $portfolio->loadCount('proposals');

        return view('portfolio.show', [
            'portfolio' => $portfolio,
            'relatedProposals' => $portfolio->proposals()->with(['job', 'employer'])->latest()->take(8)->get(),
        ]);
    }

    public function edit(Portfolio $portfolio): View
    {
        return view('portfolio.edit', ['portfolio' => $portfolio]);
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update($this->portfolioPayload($request));

        return redirect()->route('portfolio.show', $portfolio)->with('success', 'Portfolio piece updated.');
    }

    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        if ($portfolio->proposals()->exists()) {
            return back()->with('error', 'Portfolio pieces linked to proposals cannot be deleted.');
        }

        $portfolio->delete();

        return redirect()->route('portfolio.index')->with('success', 'Portfolio piece deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function portfolioPayload(StorePortfolioRequest|UpdatePortfolioRequest $request): array
    {
        $validated = $request->validated();
        $validated['tags'] = collect(explode(',', $request->input('tags_text', '')))
            ->map(static fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
        $validated['tech_stack'] = collect(explode(',', $request->input('tech_stack_text', '')))
            ->map(static fn (string $tech) => trim($tech))
            ->filter()
            ->values()
            ->all();
        $validated['tags'] = $validated['tags'] === [] ? null : $validated['tags'];
        $validated['tech_stack'] = $validated['tech_stack'] === [] ? null : $validated['tech_stack'];
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        foreach (['loom_url', 'live_url', 'github_url', 'client_name', 'client_location', 'outcome_summary', 'sort_order'] as $key) {
            $validated[$key] = $validated[$key] ?? null;
        }

        unset($validated['tags_text'], $validated['tech_stack_text']);

        return $validated;
    }
}
