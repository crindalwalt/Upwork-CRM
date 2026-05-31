@extends('layouts.app')

@section('title', 'Portfolio')

@section('subtitle', 'Curate case studies you can reuse as leverage when matching proposals to jobs.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$stats['total']" label="Portfolio pieces" icon="ti ti-folder" />
            <x-stat-card :value="$stats['featured']" label="Featured case studies" icon="ti ti-star" />
            <x-stat-card :value="$stats['with_video']" label="With Loom walkthrough" icon="ti ti-video" />
            <x-stat-card :value="$stats['used']" label="Used in proposals" icon="ti ti-link" />
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('portfolio.index') }}" class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_170px_180px_auto]">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Search</span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Title, client, or outcome" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Tag</span>
                    <select name="tag" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Any tag</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}" @selected($filters['tag'] === $tag)>{{ $tag }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Featured</span>
                    <select name="featured" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Any</option>
                        <option value="1" @selected($filters['featured'] === '1')>Featured only</option>
                        <option value="0" @selected($filters['featured'] === '0')>Not featured</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Sort</span>
                    <select name="sort" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="manual" @selected($filters['sort'] === 'manual')>Manual order</option>
                        <option value="recent" @selected($filters['sort'] === 'recent')>Recently added</option>
                        <option value="most_used" @selected($filters['sort'] === 'most_used')>Most used</option>
                    </select>
                </label>

                <div class="flex items-end gap-3">
                    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800">Filter</button>
                    <a href="{{ route('portfolio.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                        <i class="ti ti-plus"></i>
                        <span>Add case study</span>
                    </a>
                </div>
            </form>
        </section>

        @if ($portfolioItems->isEmpty())
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <x-empty-state icon="ti ti-folder-off" title="No portfolio pieces match your filters" description="Add a case study to improve leverage matching inside proposals.">
                    <a href="{{ route('portfolio.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add case study</a>
                </x-empty-state>
            </section>
        @else
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($portfolioItems as $portfolio)
                    <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                @if ($portfolio->is_featured)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-800">Featured</span>
                                @endif
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700">Order {{ $portfolio->sort_order ?? 0 }}</span>
                            </div>
                            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $portfolio->proposals_count }} uses</span>
                        </div>

                        <h2 class="mt-4 font-display text-lg font-medium text-gray-900">
                            <a href="{{ route('portfolio.show', $portfolio) }}" class="hover:text-violet-700">{{ $portfolio->title }}</a>
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ \Illuminate\Support\Str::limit($portfolio->description, 150) }}</p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (collect($portfolio->tags)->take(4) as $tag)
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $tag }}</span>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3 text-sm text-gray-500">
                            <span>{{ $portfolio->client_name ?? 'Internal project' }}</span>
                            <span>{{ $portfolio->client_location ?? 'No location' }}</span>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <a href="{{ route('portfolio.show', $portfolio) }}" class="text-sm font-semibold text-violet-700 hover:text-violet-800">View case study</a>
                            <div class="flex items-center gap-2 text-gray-500">
                                @if ($portfolio->loom_url)
                                    <a href="{{ $portfolio->loom_url }}" target="_blank" rel="noreferrer" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Open Loom">
                                        <i class="ti ti-video"></i>
                                    </a>
                                @endif
                                <a href="{{ route('portfolio.edit', $portfolio) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="rounded-xl border border-gray-200 bg-white px-6 py-4 shadow-sm">
                {{ $portfolioItems->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endsection
