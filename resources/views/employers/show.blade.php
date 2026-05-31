@extends('layouts.app')

@section('title', 'Employer Detail')

@section('subtitle', 'Review trust signals, linked opportunities, and proposal history in one place.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $employer->flagColor() === 'green' ? 'bg-emerald-100 text-emerald-700' : ($employer->flagColor() === 'yellow' ? 'bg-amber-100 text-amber-800' : ($employer->flagColor() === 'red' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">{{ ucfirst($employer->flagColor()) }}</span>
                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $employer->payment_verified ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">{{ $employer->payment_verified ? 'Payment verified' : 'Payment unverified' }}</span>
                        </div>
                        <h2 class="mt-4 font-display text-2xl font-semibold text-gray-900">{{ $employer->name }}</h2>
                        <p class="mt-2 text-sm text-gray-500">{{ $employer->location ?? 'Unknown location' }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($employer->upwork_url)
                            <a href="{{ $employer->upwork_url }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700">
                                <i class="ti ti-external-link"></i>
                                <span>Open Upwork</span>
                            </a>
                        @endif
                        <a href="{{ route('employers.edit', $employer) }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Quality score</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ number_format($employer->qualityScore(), 1) }}/10</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Total spent</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ $employer->total_spent !== null ? '$'.number_format((float) $employer->total_spent, 0) : '—' }}</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Hire rate</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ $employer->hire_rate !== null ? number_format((float) $employer->hire_rate, 1).'%' : '—' }}</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Reviews</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ $employer->reviews_count ?? 0 }}</div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Profile details</div>
                        <div class="mt-3 space-y-2 text-sm text-gray-600">
                            <div class="flex items-center justify-between gap-3">
                                <span>Open jobs</span>
                                <span class="font-medium text-gray-900">{{ $employer->open_jobs_count ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span>Member since</span>
                                <span class="font-medium text-gray-900">{{ $employer->member_since?->format('M Y') ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span>Tracked jobs</span>
                                <span class="font-medium text-gray-900">{{ $employer->jobs_count }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span>Tracked proposals</span>
                                <span class="font-medium text-gray-900">{{ $employer->proposals_count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Internal notes</div>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ $employer->internal_notes ?: 'No internal notes recorded yet.' }}</p>
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Pipeline snapshot</div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Jobs</div>
                            <div class="mt-1 text-xl font-semibold text-gray-900">{{ $employer->jobs_count }}</div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-sm text-gray-500">Proposals</div>
                            <div class="mt-1 text-xl font-semibold text-gray-900">{{ $employer->proposals_count }}</div>
                        </div>
                    </div>
                </section>
            </aside>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-display text-lg font-medium text-gray-800">Recent jobs</h2>
                </div>

                @if ($recentJobs->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="ti ti-briefcase-off" title="No jobs linked yet" description="Attach jobs to this employer to track opportunity quality over time." />
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($recentJobs as $job)
                            <a href="{{ route('jobs.show', $job) }}" class="block px-6 py-4 hover:bg-gray-50">
                                <div class="font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($job->title, 70) }}</div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                    <span class="rounded-full bg-violet-50 px-2.5 py-1 font-semibold text-violet-700">{{ $job->niche?->label() ?? 'Other' }}</span>
                                    <span>{{ $job->budgetDisplay() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="font-display text-lg font-medium text-gray-800">Recent proposals</h2>
                </div>

                @if ($recentProposals->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="ti ti-file-off" title="No proposals linked yet" description="Once proposals reference this employer, they will appear here." />
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($recentProposals as $proposal)
                            <a href="{{ route('proposals.show', $proposal) }}" class="block px-6 py-4 hover:bg-gray-50">
                                <div class="font-medium text-gray-900">{{ $proposal->job?->title ?? 'Untitled job' }}</div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                    <x-badge :status="$proposal->status" />
                                    <span>AI {{ $proposal->ai_score ?? '—' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </div>
@endsection
