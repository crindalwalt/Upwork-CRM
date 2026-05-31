@extends('layouts.app')

@section('title', 'Job Detail')

@section('subtitle', 'Review the opportunity, score quality, and see which proposals already target it.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full bg-violet-50 px-2.5 py-1 font-semibold text-violet-700">{{ $job->niche?->label() ?? 'Other' }}</span>
                            @if ($job->difficulty)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700">{{ ucfirst($job->difficulty->value) }}</span>
                            @endif
                            @if ($job->is_featured)
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-800">Featured</span>
                            @endif
                        </div>
                        <h2 class="mt-4 font-display text-2xl font-semibold text-gray-900">{{ $job->title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ $job->description }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($job->url)
                            <a href="{{ $job->url }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700">
                                <i class="ti ti-external-link"></i>
                                <span>Open listing</span>
                            </a>
                        @endif
                        <a href="{{ route('jobs.edit', $job) }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Budget</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $job->budgetDisplay() }}</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Posted</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $job->posted_at?->format('M j, Y g:i A') ?? 'Unknown' }}</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Competition</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $job->proposals_count_at_time ?? 0 }} proposals at post time</div>
                    </div>
                </div>

                @if (! empty($job->required_skills))
                    <div class="mt-6">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Required skills</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($job->required_skills as $skill)
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            <aside class="space-y-6">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">AI score</div>
                            <h3 class="mt-2 font-display text-lg font-medium text-gray-800">Qualification summary</h3>
                        </div>
                        <x-score-badge :score="$score['score']" large />
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">{{ $score['reasoning'] }}</p>

                    @if (! empty($score['flags']))
                        <div class="mt-4 space-y-2 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            @foreach ($score['flags'] as $flag)
                                <div class="flex items-start gap-2">
                                    <i class="ti ti-alert-triangle mt-0.5"></i>
                                    <span>{{ $flag }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Employer</div>
                    <div class="mt-3 text-lg font-semibold text-gray-900">{{ $job->employer?->name ?? 'Independent client' }}</div>
                    <div class="mt-3 space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-between gap-3">
                            <span>Location</span>
                            <span class="font-medium text-gray-900">{{ $job->employer?->location ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Hire rate</span>
                            <span class="font-medium text-gray-900">{{ $job->employer?->hire_rate !== null ? number_format((float) $job->employer->hire_rate, 1).'%' : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Total spent</span>
                            <span class="font-medium text-gray-900">{{ $job->employer?->total_spent !== null ? '$'.number_format((float) $job->employer->total_spent, 0) : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Payment verified</span>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $job->employer?->payment_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $job->employer?->payment_verified ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>

                    @if ($job->employer && Route::has('employers.show'))
                        <a href="{{ route('employers.show', $job->employer) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-800">
                            <span>View employer profile</span>
                            <i class="ti ti-arrow-right"></i>
                        </a>
                    @endif
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Best portfolio match</div>
                    @if ($portfolioMatch)
                        <div class="mt-3 rounded-2xl border border-violet-100 bg-violet-50 p-4">
                            <div class="text-sm font-semibold text-violet-900">{{ $portfolioMatch->title }}</div>
                            <p class="mt-2 text-sm text-violet-700">{{ $portfolioMatch->outcome_summary ?: 'This case study is the strongest proof point for the opportunity.' }}</p>
                            @if (Route::has('portfolio.show'))
                                <a href="{{ route('portfolio.show', $portfolioMatch) }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-800">
                                    <span>Open portfolio case study</span>
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 text-sm text-gray-400">No portfolio item currently matches this niche.</div>
                    @endif
                </section>
            </aside>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="font-display text-lg font-medium text-gray-800">Related proposals</h2>
                    <p class="mt-1 text-sm text-gray-400">Every proposal that references this job.</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $proposals->count() }}</span>
            </div>

            @if ($proposals->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-file-off" title="No proposals against this job yet" description="When you create a proposal for this opportunity, it will show up here." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Proposal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">AI score</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($proposals as $proposal)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600">{{ \Illuminate\Support\Str::limit($proposal->cover_letter ?? 'Untitled proposal', 70) }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $proposal->user?->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4"><x-badge :status="$proposal->status" /></td>
                                    <td class="px-6 py-4"><x-score-badge :score="$proposal->ai_score" /></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('proposals.show', $proposal) }}" class="text-sm font-semibold text-violet-700 hover:text-violet-800">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
