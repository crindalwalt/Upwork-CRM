@extends('layouts.app')

@section('title', 'AI Tools')

@section('subtitle', 'See which jobs qualify, which proposals fall below threshold, and whether the AI stack is configured.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$minAiScore" label="Minimum AI score" icon="ti ti-brain" />
            <x-stat-card :value="$dailyProposalTarget" label="Daily proposal target" icon="ti ti-target-arrow" />
            <x-stat-card :value="$connectsRemaining" label="Connects remaining" icon="ti ti-bolt" />
            <x-stat-card :value="$openAiKeyConfigured ? 'Configured' : 'Missing'" label="OpenAI key" icon="ti ti-key" />
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                    <div>
                        <h2 class="font-display text-lg font-medium text-gray-800">Job scoring workbench</h2>
                        <p class="mt-1 text-sm text-gray-400">Recent jobs scored against budget, trust signals, competition, and portfolio fit.</p>
                    </div>
                </div>

                @if ($scoredJobs->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="ti ti-brain" title="No jobs available for scoring" description="Track a few jobs first, then the scoring workbench will populate automatically." />
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Job</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Employer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Reasoning</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Best match</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($scoredJobs as $entry)
                                    <tr>
                                        <td class="px-6 py-4 align-top">
                                            <div class="font-medium text-gray-900">
                                                @if (Route::has('jobs.show'))
                                                    <a href="{{ route('jobs.show', $entry['job']) }}" class="hover:text-violet-700">{{ \Illuminate\Support\Str::limit($entry['job']->title, 60) }}</a>
                                                @else
                                                    {{ \Illuminate\Support\Str::limit($entry['job']->title, 60) }}
                                                @endif
                                            </div>
                                            <div class="mt-2 text-xs text-gray-400">{{ $entry['job']->budgetDisplay() }}</div>
                                        </td>
                                        <td class="px-6 py-4 align-top text-gray-600">{{ $entry['job']->employer?->name ?? 'Independent client' }}</td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="flex items-center gap-2">
                                                <x-score-badge :score="$entry['score']['score']" />
                                                @if (($entry['score']['score'] ?? 0) >= $minAiScore)
                                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Ready</span>
                                                @else
                                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Below target</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top text-sm text-gray-600">
                                            <p>{{ $entry['score']['reasoning'] }}</p>
                                            @if (! empty($entry['score']['flags']))
                                                <div class="mt-2 space-y-1 text-xs text-red-600">
                                                    @foreach ($entry['score']['flags'] as $flag)
                                                        <div>{{ $flag }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            @if ($entry['match'])
                                                <div class="rounded-2xl border border-violet-100 bg-violet-50 p-3">
                                                    <div class="text-sm font-semibold text-violet-900">{{ $entry['match']->title }}</div>
                                                    <div class="mt-1 text-xs text-violet-700">{{ $entry['match']->outcome_summary ?: 'Matched by niche tag alignment.' }}</div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">No portfolio match</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>

            <aside class="space-y-6">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Configuration snapshot</div>
                    <div class="mt-4 space-y-3 text-sm text-gray-600">
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <span>OpenAI model</span>
                            <span class="font-semibold text-gray-900">{{ $openAiModel ?: 'Not set' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <span>API key</span>
                            <span class="font-semibold {{ $openAiKeyConfigured ? 'text-emerald-700' : 'text-red-700' }}">{{ $openAiKeyConfigured ? 'Configured' : 'Missing' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <span>Proposal threshold</span>
                            <span class="font-semibold text-gray-900">{{ $minAiScore }}/10</span>
                        </div>
                    </div>

                    @if (! $openAiKeyConfigured)
                        <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            The AI workbench can still score jobs locally, but any future provider-backed features will stay unavailable until an API key is configured.
                        </div>
                    @endif

                    @if (auth()->user()?->isAdmin() && Route::has('settings.index'))
                        <a href="{{ route('settings.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-800">
                            <span>Manage AI settings</span>
                            <i class="ti ti-arrow-right"></i>
                        </a>
                    @endif
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="font-display text-lg font-medium text-gray-800">Low-score proposals</h2>
                        <p class="mt-1 text-sm text-gray-400">Existing proposals below the current AI threshold.</p>
                    </div>

                    @if ($lowScoreProposals->isEmpty())
                        <div class="p-6">
                            <x-empty-state icon="ti ti-chart-dots" title="No low-score proposals" description="Everything with an AI score currently clears the configured threshold.">
                                @if (Route::has('proposals.index'))
                                    <a href="{{ route('proposals.index') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Review proposals</a>
                                @endif
                            </x-empty-state>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($lowScoreProposals as $proposal)
                                <a href="{{ route('proposals.show', $proposal) }}" class="block px-6 py-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $proposal->job?->title ?? 'Untitled proposal' }}</div>
                                            <div class="mt-1 text-sm text-gray-400">{{ $proposal->employer?->name ?? 'Independent client' }}</div>
                                        </div>
                                        <x-score-badge :score="$proposal->ai_score" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            </aside>
        </section>
    </div>
@endsection
