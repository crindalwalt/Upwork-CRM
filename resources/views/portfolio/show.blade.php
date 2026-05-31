@extends('layouts.app')

@section('title', 'Portfolio Detail')

@section('subtitle', 'Review the case study, supporting links, and every proposal that leveraged it.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            @if ($portfolio->is_featured)
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-800">Featured</span>
                            @endif
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700">Order {{ $portfolio->sort_order ?? 0 }}</span>
                            <span class="rounded-full bg-violet-50 px-2.5 py-1 font-semibold text-violet-700">{{ $portfolio->proposals_count }} proposal links</span>
                        </div>
                        <h2 class="mt-4 font-display text-2xl font-semibold text-gray-900">{{ $portfolio->title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ $portfolio->description }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('portfolio.edit', $portfolio) }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">
                            <i class="ti ti-edit"></i>
                            <span>Edit</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Client</div>
                        <div class="mt-2 text-sm font-semibold text-gray-900">{{ $portfolio->client_name ?? 'Internal project' }}</div>
                        <div class="mt-1 text-sm text-gray-500">{{ $portfolio->client_location ?? 'Unknown location' }}</div>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Outcome summary</div>
                        <div class="mt-2 text-sm text-gray-700">{{ $portfolio->outcome_summary ?: 'No outcome summary recorded yet.' }}</div>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Tags</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse ($portfolio->tags ?? [] as $tag)
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $tag }}</span>
                            @empty
                                <span class="text-sm text-gray-400">No tags added.</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Tech stack</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @forelse ($portfolio->tech_stack ?? [] as $tech)
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $tech }}</span>
                            @empty
                                <span class="text-sm text-gray-400">No tech stack recorded.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Links</div>
                    <div class="mt-4 space-y-3 text-sm">
                        @foreach ([['label' => 'Loom walkthrough', 'value' => $portfolio->loom_url, 'icon' => 'ti ti-video'], ['label' => 'Live URL', 'value' => $portfolio->live_url, 'icon' => 'ti ti-world'], ['label' => 'GitHub URL', 'value' => $portfolio->github_url, 'icon' => 'ti ti-brand-github']] as $link)
                            <div class="flex items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <span class="inline-flex items-center gap-2 text-gray-600">
                                    <i class="{{ $link['icon'] }}"></i>
                                    <span>{{ $link['label'] }}</span>
                                </span>
                                @if ($link['value'])
                                    <a href="{{ $link['value'] }}" target="_blank" rel="noreferrer" class="font-semibold text-violet-700 hover:text-violet-800">Open</a>
                                @else
                                    <span class="text-gray-400">Missing</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>

                @can('delete', $portfolio)
                    <section class="rounded-xl border border-red-200 bg-red-50 p-6 shadow-sm">
                        <div class="text-sm font-semibold text-red-800">Danger zone</div>
                        <p class="mt-2 text-sm text-red-700">Delete is blocked once proposals rely on this proof point.</p>
                        <div class="mt-4">
                            <x-confirm-delete :action="route('portfolio.destroy', $portfolio)" title="Delete portfolio piece" message="This permanently removes the case study if it is not attached to any proposal." class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                                <i class="ti ti-trash"></i>
                                <span>Delete case study</span>
                            </x-confirm-delete>
                        </div>
                    </section>
                @endcan
            </aside>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="font-display text-lg font-medium text-gray-800">Related proposals</h2>
            </div>

            @if ($relatedProposals->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-link-off" title="No proposals use this case study yet" description="Once a proposal selects this portfolio piece as leverage, it will appear here." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Proposal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Employer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($relatedProposals as $proposal)
                                <tr>
                                    <td class="px-6 py-4 text-gray-700">{{ $proposal->job?->title ?? 'Untitled job' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $proposal->employer?->name ?? 'Independent client' }}</td>
                                    <td class="px-6 py-4"><x-badge :status="$proposal->status" /></td>
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
