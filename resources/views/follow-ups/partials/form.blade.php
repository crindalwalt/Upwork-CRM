@php
    $selectedProposalId = old('proposal_id', $selectedProposalId ?? $followUp?->proposal_id);
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if (! empty($method) && $method !== 'POST')
                @method($method)
            @endif

            <label class="block">
                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Proposal</span>
                <select name="proposal_id" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                    @foreach ($proposals as $proposal)
                        <option value="{{ $proposal->id }}" @selected($selectedProposalId === $proposal->id)>
                            {{ $proposal->job?->title ?? 'Untitled proposal' }}{{ $proposal->employer ? ' • '.$proposal->employer->name : '' }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->first('proposal_id'))
                    <span class="mt-2 block text-sm text-red-600">{{ $errors->first('proposal_id') }}</span>
                @endif
            </label>

            <div class="grid gap-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Type</span>
                    <select name="type" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(old('type', $followUp?->type?->value) === $type->value)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('type'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('type') }}</span>
                    @endif
                </label>

                <x-form-input name="scheduled_at" label="Scheduled at" type="datetime-local" :value="old('scheduled_at', $followUp?->scheduled_at?->format('Y-m-d\TH:i'))" required />
            </div>

            <x-form-input name="outcome_note" label="Outcome note" type="textarea" rows="5" :value="old('outcome_note', $followUp?->outcome_note)" placeholder="Optional context, reminder, or completed outcome." />

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ti ti-device-floppy"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        </form>
    </section>

    <aside class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div>
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Execution rhythm</div>
            <h2 class="mt-2 font-display text-lg font-medium text-gray-800">Make the next move explicit</h2>
            <p class="mt-2 text-sm text-gray-500">This list is most useful when every proposal has a concrete next action with a timestamp, not just an intention.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
            <div class="font-semibold text-gray-800">Good follow-up hygiene</div>
            <div class="mt-3 space-y-2">
                <p>Use short, outcome-based notes instead of generic reminders.</p>
                <p>Mark items done as soon as the action happens so overdue counts stay trustworthy.</p>
                <p>Attach follow-ups to the real proposal record so the conversation timeline stays coherent.</p>
            </div>
        </div>
    </aside>
</div>
