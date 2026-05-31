<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProposalNoteController extends Controller
{
    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorize('update', $proposal);

        $validated = $request->validate([
            'loom_script' => ['nullable', 'string'],
            'talking_points_text' => ['nullable', 'string'],
            'internal_note' => ['nullable', 'string'],
        ]);

        $talkingPoints = collect(preg_split('/\r\n|\r|\n/', $validated['talking_points_text'] ?? ''))
            ->map(static fn (string $point) => trim($point))
            ->filter()
            ->values()
            ->all();

        if (blank($validated['loom_script'] ?? null) && blank($validated['internal_note'] ?? null) && $talkingPoints === []) {
            throw ValidationException::withMessages([
                'internal_note' => 'Add at least one note field before saving.',
            ]);
        }

        $proposal->proposalNotes()->create([
            'user_id' => $request->user()->id,
            'loom_script' => $validated['loom_script'] ?: null,
            'talking_points' => $talkingPoints === [] ? null : $talkingPoints,
            'internal_note' => $validated['internal_note'] ?: null,
        ]);

        return back()->with('success', 'Note added to proposal.');
    }

    public function destroy(Proposal $proposal, ProposalNote $note): RedirectResponse
    {
        $this->authorize('update', $proposal);

        abort_unless($note->proposal_id === $proposal->id, 404);

        $note->delete();

        return back()->with('success', 'Proposal note deleted.');
    }
}
