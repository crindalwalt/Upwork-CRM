@extends('layouts.app')

@section('title', 'Proposals')

@section('content')
    <div class="space-y-5">
        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('proposals.index') }}" class="grid gap-3 xl:grid-cols-[160px_160px_170px_170px_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">Status</span>
                    <select name="status" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">Niche</span>
                    <select name="niche" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">All niches</option>
                        @foreach ($niches as $niche)
                            <option value="{{ $niche->value }}" @selected($filters['niche'] === $niche->value)>{{ $niche->label() }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">Date from</span>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <label class="block">
                    <span class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">Date to</span>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <label class="block">
                    <span class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">Search</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Job, employer, or cover letter" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Filter</button>
                    @if (Route::has('proposals.create'))
                        <a href="{{ route('proposals.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-violet-700">
                            <i class="ti ti-plus"></i>
                            <span>Add proposal</span>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="grid gap-3 md:grid-cols-5">
            @php
                $statLinks = [
                    ['label' => 'Sent', 'value' => $stats['total_sent'], 'status' => 'sent', 'classes' => 'border border-gray-200 bg-white text-gray-600'],
                    ['label' => 'Viewed', 'value' => $stats['total_viewed'], 'status' => 'viewed', 'classes' => 'border border-gray-200 bg-gray-50 text-gray-700'],
                    ['label' => 'Replied', 'value' => $stats['total_replied'], 'status' => 'replied', 'classes' => 'border border-gray-300 bg-gray-100 text-gray-800'],
                    ['label' => 'Won', 'value' => $stats['total_won'], 'status' => 'won', 'classes' => 'border border-gray-900 bg-gray-900 text-white'],
                    ['label' => 'Lost', 'value' => $stats['total_lost'], 'status' => 'lost', 'classes' => 'border border-gray-200 bg-white text-gray-500'],
                ];
            @endphp

            @foreach ($statLinks as $stat)
                <a href="{{ route('proposals.index', array_filter([...$filters, 'status' => $stat['status']])) }}" class="rounded-xl px-4 py-3 shadow-sm {{ $stat['classes'] }} {{ $filters['status'] === $stat['status'] ? 'ring-1 ring-gray-900/10' : '' }}">
                    <div class="text-2xl font-semibold font-display">{{ $stat['value'] }}</div>
                    <div class="mt-1 text-[0.68rem] font-semibold uppercase tracking-[0.16em]">{{ $stat['label'] }}</div>
                </a>
            @endforeach
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if ($proposals->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-search-off" title="No proposals match your filters" description="Try widening your search or reset the filter set.">
                        <a href="{{ route('proposals.index') }}" class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-black">Reset filters</a>
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Job</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Employer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">AI score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Loom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Connects</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Sent</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($proposals as $proposal)
                                <tr>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('proposals.show', $proposal) }}" class="hover:text-violet-700">{{ \Illuminate\Support\Str::limit($proposal->job?->title ?? 'Untitled job', 50) }}</a>
                                        </div>
                                        <div class="mt-2">
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $proposal->job?->niche?->label() ?? 'Other' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-800">{{ $proposal->employer?->name ?? 'Independent client' }}</div>
                                        <div class="mt-1 text-sm text-gray-400">{{ $proposal->employer?->location ?? 'No location' }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-top"><x-badge :status="$proposal->status" /></td>
                                    <td class="px-6 py-4 align-top"><x-score-badge :score="$proposal->ai_score" /></td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            @if ($proposal->loom_viewed)
                                                <i class="ti ti-eye text-gray-700"></i>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                            <span class="text-sm text-gray-400">{{ $proposal->loom_view_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $proposal->connects_spent }}</td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $proposal->sent_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2 text-gray-500">
                                            <a href="{{ route('proposals.show', $proposal) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('proposals.edit', $proposal) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @can('delete', $proposal)
                                                <x-confirm-delete :action="route('proposals.destroy', $proposal)" title="Delete proposal" message="This will permanently remove the proposal and its related notes and follow-ups." class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
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
                    {{ $proposals->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>
    </div>
@endsection
