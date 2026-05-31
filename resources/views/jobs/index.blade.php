@extends('layouts.app')

@section('title', 'Jobs')

@section('subtitle', 'Track opportunities, compare competition, and move the strongest work into proposals.')

@section('content')
    @php
        $visibleAverageScore = collect($jobScores->values())->filter()->avg();
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$jobs->total()" label="Tracked jobs" icon="ti ti-briefcase" />
            <x-stat-card :value="$jobs->getCollection()->filter(fn ($job) => $job->isRecent())->count()" label="Recent on this page" icon="ti ti-sparkles" />
            <x-stat-card :value="$jobs->getCollection()->where('is_featured', true)->count()" label="Featured on this page" icon="ti ti-bolt" />
            <x-stat-card :value="$visibleAverageScore ? number_format($visibleAverageScore, 1) : '—'" label="Average score on this page" icon="ti ti-chart-dots-3" />
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('jobs.index') }}" class="grid gap-3 xl:grid-cols-[180px_180px_180px_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Niche</span>
                    <select name="niche" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">All niches</option>
                        @foreach ($niches as $niche)
                            <option value="{{ $niche->value }}" @selected($filters['niche'] === $niche->value)>{{ $niche->label() }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Budget type</span>
                    <select name="budget_type" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Any budget</option>
                        @foreach ($budgetTypes as $budgetType)
                            <option value="{{ $budgetType->value }}" @selected($filters['budget_type'] === $budgetType->value)>{{ ucfirst($budgetType->value) }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Sort</span>
                    <select name="sort" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                        <option value="highest_budget" @selected($filters['sort'] === 'highest_budget')>Highest budget</option>
                        <option value="most_proposals" @selected($filters['sort'] === 'most_proposals')>Most internal proposals</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Search</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Job title, description, or employer" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                    <a href="{{ route('jobs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="ti ti-plus"></i>
                        <span>Add job</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if ($jobs->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-briefcase-off" title="No jobs match your filters" description="Try widening the search or add a new job to seed the pipeline.">
                        <a href="{{ route('jobs.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add job</a>
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Opportunity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Employer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Budget</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Competition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">AI score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Posted</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($jobs as $job)
                                <tr>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('jobs.show', $job) }}" class="hover:text-violet-700">{{ \Illuminate\Support\Str::limit($job->title, 60) }}</a>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                            <span class="rounded-full bg-violet-50 px-2.5 py-1 font-semibold text-violet-700">{{ $job->niche?->label() ?? 'Other' }}</span>
                                            @if ($job->difficulty)
                                                <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700">{{ ucfirst($job->difficulty->value) }}</span>
                                            @endif
                                            @if ($job->is_featured)
                                                <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-800">Featured</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-800">{{ $job->employer?->name ?? 'Independent client' }}</div>
                                        <div class="mt-1 text-sm text-gray-400">{{ $job->employer?->location ?? 'No location' }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $job->budgetDisplay() }}</td>
                                    <td class="px-6 py-4 align-top">
                                        @php
                                            $competition = $job->proposals_count_at_time ?? 0;
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $competition > 20 ? 'bg-red-100 text-red-700' : ($competition > 10 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700') }}">
                                            {{ $competition }} at post time
                                        </span>
                                        <div class="mt-2 text-xs text-gray-400">{{ $job->proposals_count }} internal proposals</div>
                                    </td>
                                    <td class="px-6 py-4 align-top"><x-score-badge :score="$jobScores->get($job->id)" /></td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $job->posted_at?->diffForHumans() ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2 text-gray-500">
                                            <a href="{{ route('jobs.show', $job) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('jobs.edit', $job) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @can('delete', $job)
                                                <x-confirm-delete :action="route('jobs.destroy', $job)" title="Delete job" message="This removes the job unless it already has proposals attached." class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                                    <i class="ti ti-trash"></i>
                                                </x-confirm-delete>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $jobs->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>
    </div>
@endsection
