@extends('layouts.app')

@php
    $statusClasses = match ($proposal->status) {
        \App\Enums\ProposalStatus::Won => 'bg-emerald-100 text-emerald-800',
        \App\Enums\ProposalStatus::Viewed => 'bg-amber-100 text-amber-800',
        \App\Enums\ProposalStatus::Lost => 'bg-red-100 text-red-800',
        \App\Enums\ProposalStatus::Sent => 'bg-blue-100 text-blue-800',
        \App\Enums\ProposalStatus::Replied => 'bg-purple-100 text-purple-800',
        \App\Enums\ProposalStatus::InterviewScheduled => 'bg-slate-100 text-slate-800',
        \App\Enums\ProposalStatus::Withdrawn => 'bg-orange-100 text-orange-800',
        default => 'bg-gray-100 text-gray-700',
    };
    $loomEmbedUrl = $proposal->loom_url ? str_replace('/share/', '/embed/', $proposal->loom_url) : null;
@endphp

@section('title', $proposal->job?->title ?? 'Proposal details')

@section('content')
    <div x-data="{
        statusValue: @js($proposal->status->value),
        statusLabel: @js($proposal->status->label()),
        statusClasses: @js($statusClasses),
        viewCount: {{ $proposal->loom_view_count }},
        async updateStatus(status) {
            const response = await fetch(@js(route('proposals.updateStatus', $proposal)), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status }),
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            this.statusValue = payload.status;
            this.statusLabel = payload.label;
            this.statusClasses = payload.color;
        },
        async recordView() {
            const response = await fetch(@js(route('proposals.loomView', $proposal)), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            this.viewCount = payload.view_count;
        },
    }" class="grid gap-6 xl:grid-cols-[2fr_1fr_1fr]">
        <section class="space-y-6">
            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $proposal->job?->niche?->label() ?? 'Other' }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClasses" x-text="statusLabel"></span>
                            <x-score-badge :score="$proposal->ai_score" />
                        </div>
                        <h2 class="mt-4 text-2xl font-semibold text-gray-900 font-display">{{ $proposal->job?->title ?? 'Untitled job' }}</h2>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($statuses as $status)
                        <button type="button" @click="updateStatus(@js($status->value))" :class="statusValue === @js($status->value) ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="rounded-xl px-3 py-2 text-sm font-medium transition">
                            {{ $status->label() }}
                        </button>
                    @endforeach
                </div>
            </article>

            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 font-display">Loom</h2>
                        <p class="mt-1 text-sm text-gray-400">Video walkthrough and view tracking.</p>
                    </div>
                    @if ($proposal->loom_url)
                        <button type="button" @click="recordView()" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Mark as viewed</button>
                    @endif
                </div>

                @if ($loomEmbedUrl)
                    <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200">
                        <iframe src="{{ $loomEmbedUrl }}" class="h-80 w-full" allowfullscreen></iframe>
                    </div>
                    <div class="mt-4 flex items-center gap-3 text-sm text-gray-500">
                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-blue-800">
                            <i class="ti ti-eye"></i>
                            <span x-text="viewCount"></span>
                        </span>
                        <span>{{ $proposal->loom_viewed_at?->diffForHumans() ?? 'Not viewed yet' }}</span>
                    </div>
                @else
                    <div class="mt-6">
                        <x-empty-state icon="ti ti-video-off" title="No Loom attached" description="Add a Loom URL to track views and embed the walkthrough here." />
                    </div>
                @endif
            </article>

            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Cover letter</h2>
                <pre class="mt-4 whitespace-pre-wrap rounded-2xl bg-gray-50 p-4 font-mono text-sm text-gray-700">{{ $proposal->cover_letter ?: 'No cover letter saved yet.' }}</pre>
            </article>

            @if ($proposal->has_leverage && $proposal->leveragePortfolio)
                <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-medium text-gray-800 font-display">Portfolio leverage</h2>
                    <div class="mt-4 rounded-2xl border border-violet-100 bg-violet-50 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-semibold text-violet-900">{{ $proposal->leveragePortfolio->title }}</h3>
                                <p class="mt-2 text-sm text-violet-700">{{ $proposal->leveragePortfolio->outcome_summary ?: 'No outcome summary recorded.' }}</p>
                            </div>
                        </div>
                        @if ($proposal->leveragePortfolio->tags)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($proposal->leveragePortfolio->tags as $tag)
                                    <span class="rounded-full bg-white/80 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ \Illuminate\Support\Str::headline($tag) }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($proposal->leverage_notes)
                            <p class="mt-4 text-sm text-violet-800">{{ $proposal->leverage_notes }}</p>
                        @endif
                    </div>
                </article>
            @endif

            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Bid info</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Fixed bid</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $proposal->bid_amount ? '$'.number_format((float) $proposal->bid_amount, 2) : '—' }}</div>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Hourly bid</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $proposal->bid_hourly_rate ? '$'.number_format((float) $proposal->bid_hourly_rate, 2).'/hr' : '—' }}</div>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Won amount</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $proposal->won_amount ? '$'.number_format((float) $proposal->won_amount, 2) : '—' }}</div>
                    </div>
                </div>
            </article>
        </section>

        <section class="space-y-6">
            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Activity</h2>
                @php
                    $timeline = [
                        ['label' => 'Proposal created', 'date' => $proposal->created_at, 'classes' => 'bg-gray-100 text-gray-700'],
                        ['label' => 'Sent', 'date' => $proposal->sent_at, 'classes' => 'bg-blue-100 text-blue-800'],
                        ['label' => 'Loom viewed', 'date' => $proposal->loom_viewed_at, 'classes' => 'bg-amber-100 text-amber-800'],
                        ['label' => 'Replied', 'date' => $proposal->replied_at, 'classes' => 'bg-purple-100 text-purple-800'],
                        ['label' => 'Interview scheduled', 'date' => $proposal->interview_at, 'classes' => 'bg-slate-100 text-slate-800'],
                        ['label' => 'Closed', 'date' => $proposal->closed_at, 'classes' => 'bg-emerald-100 text-emerald-800'],
                    ];
                @endphp
                <div class="mt-6 space-y-5">
                    @foreach ($timeline as $event)
                        @continue(! $event['date'])
                        <div class="flex gap-4">
                            <div class="mt-1 h-9 w-9 rounded-full flex items-center justify-center text-xs font-semibold {{ $event['classes'] }}">•</div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $event['label'] }}</div>
                                <div class="mt-1 text-sm text-gray-400">{{ $event['date']->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm" x-data="{ openNoteForm: false }">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 font-display">Notes</h2>
                        <p class="mt-1 text-sm text-gray-400">Internal coaching notes, Loom scripts, and talking points.</p>
                    </div>
                    <button type="button" @click="openNoteForm = !openNoteForm" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Add note</button>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($proposal->proposalNotes as $note)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    @if ($note->loom_script)
                                        <p class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($note->loom_script, 180) }}</p>
                                    @endif
                                    @if ($note->talking_points)
                                        <ul class="mt-3 space-y-1 text-sm text-gray-600">
                                            @foreach ($note->talking_points as $point)
                                                <li>• {{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if ($note->internal_note)
                                        <p class="mt-3 text-sm text-gray-600">{{ $note->internal_note }}</p>
                                    @endif
                                    <div class="mt-3 text-xs text-gray-400">Added by {{ $note->user?->name ?? 'Unknown user' }} · {{ $note->created_at->diffForHumans() }}</div>
                                </div>
                                <x-confirm-delete :action="route('proposal-notes.destroy', [$proposal, $note])" title="Delete note" message="This note will be removed from the proposal timeline." class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                    <i class="ti ti-trash"></i>
                                </x-confirm-delete>
                            </div>
                        </div>
                    @empty
                        <x-empty-state icon="ti ti-notes" title="No notes yet" description="Use notes to save scripts, talking points, or internal guidance." />
                    @endforelse
                </div>

                <div x-show="openNoteForm" x-cloak class="mt-6 rounded-2xl border border-dashed border-gray-300 p-4">
                    <form method="POST" action="{{ route('proposal-notes.store', $proposal) }}" class="space-y-4">
                        @csrf
                        <x-form-input name="loom_script" label="Loom script" type="textarea" rows="4" :value="old('loom_script')" placeholder="Paste a Loom script draft..." />
                        <x-form-input name="talking_points_text" label="Talking points" type="textarea" rows="4" :value="old('talking_points_text')" placeholder="One talking point per line" />
                        <x-form-input name="internal_note" label="Internal note" type="textarea" rows="3" :value="old('internal_note')" placeholder="Internal context or reminders..." />
                        <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Save note</button>
                    </form>
                </div>
            </article>
        </section>

        <section class="space-y-6">
            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 font-display">Follow-ups</h2>
                        <p class="mt-1 text-sm text-gray-400">Pending and completed actions tied to this proposal.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($proposal->followUps->sortBy('scheduled_at') as $followUp)
                        <div class="rounded-2xl border border-gray-200 p-4 {{ $followUp->is_done ? 'bg-gray-50 opacity-75' : '' }}">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $followUp->type->name }}</span>
                                        <span class="text-sm {{ $followUp->isOverdue() ? 'text-red-600' : 'text-gray-400' }}">{{ $followUp->scheduled_at?->format('M j, Y g:i A') }}</span>
                                    </div>
                                    @if ($followUp->outcome_note)
                                        <p class="mt-3 text-sm text-gray-600">{{ $followUp->outcome_note }}</p>
                                    @endif
                                </div>

                                @if (! $followUp->is_done && Route::has('follow-ups.complete'))
                                    <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}" class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="outcome_note" rows="2" placeholder="Outcome note (optional)" class="block w-56 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"></textarea>
                                        <button type="submit" class="w-full rounded-xl bg-violet-600 px-3 py-2 text-sm font-semibold text-white hover:bg-violet-700">Mark done</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <x-empty-state icon="ti ti-clock-hour-4" title="No follow-ups yet" description="Add the first follow-up for this proposal to keep the sequence moving." />
                    @endforelse
                </div>

                @if (Route::has('follow-ups.store'))
                    <div class="mt-6 rounded-2xl border border-dashed border-gray-300 p-4">
                        <form method="POST" action="{{ route('follow-ups.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                            <label class="block">
                                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Type</span>
                                <select name="type" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                    @foreach ($followUpTypes as $type)
                                        <option value="{{ $type->value }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Scheduled at</span>
                                <input type="datetime-local" name="scheduled_at" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                            </label>
                            <button type="submit" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add follow-up</button>
                        </form>
                    </div>
                @endif
            </article>

            <article class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-800 font-display">Metadata</h2>
                <div class="mt-6 space-y-4 text-sm text-gray-600">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Employer</div>
                        <div class="mt-1 font-medium text-gray-900">
                            @if ($proposal->employer && Route::has('employers.show'))
                                <a href="{{ route('employers.show', $proposal->employer) }}" class="hover:text-violet-700">{{ $proposal->employer->name }}</a>
                            @else
                                {{ $proposal->employer?->name ?? 'Independent client' }}
                            @endif
                        </div>
                        <div class="mt-1 text-gray-400">{{ $proposal->employer?->location ?? 'No location' }}</div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Hire rate</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->employer?->hire_rate ? $proposal->employer->hire_rate.'%' : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Total spent</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->employer?->total_spent ? '$'.$proposal->employer->total_spent : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Payment verified</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->employer?->payment_verified ? 'Yes' : 'No' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Connects spent</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->connects_spent }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Created by</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->user?->name ?? 'Unknown' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Created at</div>
                            <div class="mt-1 text-gray-900">{{ $proposal->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </div>
@endsection
