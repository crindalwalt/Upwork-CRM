<?php

namespace App\Http\Controllers;

use App\Enums\FollowUpType;
use App\Http\Requests\StoreFollowUpRequest;
use App\Http\Requests\UpdateFollowUpRequest;
use App\Models\FollowUp;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FollowUp::class, 'follow_up');
    }

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->string('status')->toString(),
            'type' => $request->string('type')->toString(),
            'search' => $request->string('search')->toString(),
        ];

        $query = FollowUp::query()->with(['proposal.job', 'proposal.employer', 'user']);

        if ($filters['status'] !== '') {
            match ($filters['status']) {
                'pending' => $query->pending(),
                'done' => $query->done(),
                'today' => $query->pending()->dueToday(),
                'overdue' => $query->overdue(),
                default => null,
            };
        }

        if ($filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($followUpQuery) use ($search): void {
                $followUpQuery
                    ->where('outcome_note', 'like', "%{$search}%")
                    ->orWhereHas('proposal.job', fn ($jobQuery) => $jobQuery->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('proposal.employer', fn ($employerQuery) => $employerQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return view('follow-ups.index', [
            'followUps' => $query->orderBy('is_done')->orderBy('scheduled_at')->paginate(18)->withQueryString(),
            'filters' => $filters,
            'types' => FollowUpType::cases(),
            'stats' => [
                'pending' => FollowUp::query()->pending()->count(),
                'today' => FollowUp::query()->pending()->dueToday()->count(),
                'overdue' => FollowUp::query()->overdue()->count(),
                'done' => FollowUp::query()->done()->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', FollowUp::class);

        return view('follow-ups.create', [
            'proposals' => $this->proposalOptions(),
            'types' => FollowUpType::cases(),
            'selectedProposalId' => $request->string('proposal_id')->toString(),
        ]);
    }

    public function store(StoreFollowUpRequest $request): RedirectResponse
    {
        $this->authorize('create', FollowUp::class);

        $followUp = FollowUp::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'is_done' => false,
            'completed_at' => null,
        ]);

        $redirectRoute = $request->filled('proposal_id') ? route('proposals.show', $followUp->proposal_id) : route('follow-ups.index');

        return redirect($redirectRoute)->with('success', 'Follow-up created.');
    }

    public function edit(FollowUp $follow_up): View
    {
        return view('follow-ups.edit', [
            'followUp' => $follow_up,
            'proposals' => $this->proposalOptions(),
            'types' => FollowUpType::cases(),
            'selectedProposalId' => (string) old('proposal_id', $follow_up->proposal_id),
        ]);
    }

    public function update(UpdateFollowUpRequest $request, FollowUp $follow_up): RedirectResponse
    {
        $follow_up->update($request->validated());

        return redirect()->route('follow-ups.index')->with('success', 'Follow-up updated.');
    }

    public function destroy(FollowUp $follow_up): RedirectResponse
    {
        $follow_up->delete();

        return redirect()->route('follow-ups.index')->with('success', 'Follow-up deleted.');
    }

    public function complete(Request $request, FollowUp $follow_up): RedirectResponse
    {
        $this->authorize('update', $follow_up);

        $validated = $request->validate([
            'outcome_note' => ['nullable', 'string'],
        ]);

        $follow_up->markDone($validated['outcome_note'] ?? null);

        return back()->with('success', 'Follow-up marked as done.');
    }

    private function proposalOptions()
    {
        return Proposal::query()
            ->with(['job', 'employer'])
            ->latest()
            ->get();
    }
}
