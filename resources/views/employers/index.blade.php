@extends('layouts.app')

@section('title', 'Employers')

@section('subtitle', 'Keep the client book clean, searchable, and ready for job and proposal reuse.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$stats['total']" label="Tracked employers" icon="ti ti-building" />
            <x-stat-card :value="$stats['verified']" label="Payment verified" icon="ti ti-shield-check" />
            <x-stat-card :value="$stats['high_quality']" label="High quality" icon="ti ti-rosette-discount-check" />
            <x-stat-card :value="$stats['flagged']" label="Flagged accounts" icon="ti ti-flag" />
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('employers.index') }}" class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_160px_170px_180px_auto]">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Search</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Company, location, or notes" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Flag</span>
                    <select name="flag" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Any</option>
                        <option value="green" @selected($filters['flag'] === 'green')>Green</option>
                        <option value="yellow" @selected($filters['flag'] === 'yellow')>Yellow</option>
                        <option value="red" @selected($filters['flag'] === 'red')>Red</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Verified</span>
                    <select name="verified" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Any</option>
                        <option value="1" @selected($filters['verified'] === '1')>Verified only</option>
                        <option value="0" @selected($filters['verified'] === '0')>Unverified only</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Sort</span>
                    <select name="sort" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                        <option value="highest_quality" @selected($filters['sort'] === 'highest_quality')>Highest quality</option>
                        <option value="most_jobs" @selected($filters['sort'] === 'most_jobs')>Most jobs</option>
                    </select>
                </label>

                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                    <a href="{{ route('employers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="ti ti-plus"></i>
                        <span>Add employer</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            @if ($employers->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-building-skyscraper" title="No employers match your filters" description="Add a client profile to link jobs, proposals, and trust signals in one place.">
                        <a href="{{ route('employers.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add employer</a>
                    </x-empty-state>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Employer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Quality</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Trust</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Pipeline</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($employers as $employer)
                                <tr>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('employers.show', $employer) }}" class="hover:text-violet-700">{{ $employer->name }}</a>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-400">{{ $employer->location ?? 'Unknown location' }}</div>
                                        <div class="mt-2 inline-flex items-center gap-2 text-xs">
                                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $employer->flagColor() === 'green' ? 'bg-emerald-100 text-emerald-700' : ($employer->flagColor() === 'yellow' ? 'bg-amber-100 text-amber-800' : ($employer->flagColor() === 'red' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">{{ ucfirst($employer->flagColor()) }}</span>
                                            @if ($employer->upwork_url)
                                                <a href="{{ $employer->upwork_url }}" target="_blank" rel="noreferrer" class="font-semibold text-violet-700 hover:text-violet-800">Upwork</a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ number_format($employer->qualityScore(), 1) }}/10</span>
                                        <div class="mt-2 text-sm text-gray-500">{{ $employer->reviews_count ?? 0 }} reviews</div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-sm text-gray-600">
                                        <div>{{ $employer->payment_verified ? 'Verified payment' : 'Unverified payment' }}</div>
                                        <div class="mt-1">Hire rate: {{ $employer->hire_rate !== null ? number_format((float) $employer->hire_rate, 1).'%' : '—' }}</div>
                                        <div class="mt-1">Spent: {{ $employer->total_spent !== null ? '$'.number_format((float) $employer->total_spent, 0) : '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-sm text-gray-600">
                                        <div>{{ $employer->jobs_count }} jobs</div>
                                        <div class="mt-1">{{ $employer->proposals_count }} proposals</div>
                                        <div class="mt-1">{{ $employer->open_jobs_count ?? 0 }} open jobs</div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2 text-gray-500">
                                            <a href="{{ route('employers.show', $employer) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('employers.edit', $employer) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @can('delete', $employer)
                                                <x-confirm-delete :action="route('employers.destroy', $employer)" title="Delete employer" message="Delete is blocked if the employer is already linked to jobs or proposals." class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 hover:text-red-700">
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
                    {{ $employers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </section>
    </div>
@endsection
