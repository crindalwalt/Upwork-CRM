@extends('layouts.app')

@section('title', 'Follow-ups')

@section('subtitle', 'Keep proposal conversations moving with visible next actions and overdue recovery.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$stats['pending']" label="Pending" icon="ti ti-clock-hour-4" />
            <x-stat-card :value="$stats['today']" label="Due today" icon="ti ti-calendar-time" />
            <x-stat-card :value="$stats['overdue']" label="Overdue" icon="ti ti-alert-circle" />
            <x-stat-card :value="$stats['done']" label="Completed" icon="ti ti-circle-check" />
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('follow-ups.index') }}" class="grid gap-3 xl:grid-cols-[180px_180px_minmax(0,1fr)_auto]">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Status</span>
                    <select name="status" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">All</option>
                        <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                        <option value="today" @selected($filters['status'] === 'today')>Due today</option>
                        <option value="overdue" @selected($filters['status'] === 'overdue')>Overdue</option>
                        <option value="done" @selected($filters['status'] === 'done')>Done</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Type</span>
                    <select name="type" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">All types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($filters['type'] === $type->value)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Search</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Proposal, employer, or outcome note" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                    <a href="{{ route('follow-ups.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="ti ti-plus"></i>
                        <span>Add follow-up</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if ($followUps->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-clock-off" title="No follow-ups match your filters" description="Create a next step on a proposal to keep the pipeline active.">
                        <a href="{{ route('follow-ups.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add follow-up</a>
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Proposal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Scheduled</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Outcome</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($followUps as $followUp)
                                <tr>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            @if ($followUp->proposal && Route::has('proposals.show'))
                                                <a href="{{ route('proposals.show', $followUp->proposal) }}" class="hover:text-violet-700">{{ $followUp->proposal->job?->title ?? 'Untitled proposal' }}</a>
                                            @else
                                                {{ $followUp->proposal->job?->title ?? 'Untitled proposal' }}
                                            @endif
                                        </div>
                                        <div class="mt-1 text-sm text-gray-400">{{ $followUp->proposal?->employer?->name ?? 'Independent client' }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $followUp->type->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 align-top text-sm text-gray-600">
                                        <div>{{ $followUp->scheduled_at?->format('M j, Y g:i A') }}</div>
                                        <div class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $followUp->is_done ? 'bg-emerald-100 text-emerald-700' : ($followUp->isOverdue() ? 'bg-red-100 text-red-700' : ($followUp->scheduled_at?->isToday() ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ $followUp->is_done ? 'Done' : ($followUp->isOverdue() ? 'Overdue' : ($followUp->scheduled_at?->isToday() ? 'Due today' : 'Upcoming')) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $followUp->user?->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 align-top text-gray-600">{{ $followUp->outcome_note ?: '—' }}</td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2 text-gray-500">
                                            @if (! $followUp->is_done)
                                                <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl border border-emerald-200 px-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">Done</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('follow-ups.edit', $followUp) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @can('delete', $followUp)
                                                <x-confirm-delete :action="route('follow-ups.destroy', $followUp)" title="Delete follow-up" message="This permanently removes the reminder and any stored outcome note." class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
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
                    {{ $followUps->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>
    </div>
@endsection
