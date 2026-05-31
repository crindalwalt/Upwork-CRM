@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card :value="$weeklyProposals" label="Total sent this week" icon="ti ti-send" />
            <x-stat-card :value="number_format($stats['view_rate'], 1).'%'" label="Loom view rate" icon="ti ti-eye" />
            <x-stat-card :value="number_format($stats['reply_rate'], 1).'%'" label="Reply rate" icon="ti ti-message-circle" />
            <x-stat-card :value="$dashboardConnectsRemaining" label="Connects remaining" icon="ti ti-bolt" />
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 font-display">Proposal momentum</h2>
                        <p class="mt-1 text-sm text-gray-400">The last 14 days of sent proposals.</p>
                    </div>
                    <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">14 days</span>
                </div>
                <div class="mt-6 h-72">
                    <canvas id="proposalChart"></canvas>
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 font-display">Due today</h2>
                        <p class="mt-1 text-sm text-gray-400">Follow-ups that need attention before the day ends.</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $todayFollowUps->count() }}</span>
                </div>

                @if ($todayFollowUps->isEmpty())
                    <div class="mt-6">
                        <x-empty-state icon="ti ti-clock-hour-4" title="No follow-ups due today" description="You're clear for today. New follow-ups will appear here as they become due." />
                    </div>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($todayFollowUps as $followUp)
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            @if (Route::has('proposals.show'))
                                                <a href="{{ route('proposals.show', $followUp->proposal) }}" class="hover:text-violet-700">
                                                    {{ \Illuminate\Support\Str::limit($followUp->proposal?->job?->title ?? 'Untitled proposal', 40) }}
                                                </a>
                                            @else
                                                {{ \Illuminate\Support\Str::limit($followUp->proposal?->job?->title ?? 'Untitled proposal', 40) }}
                                            @endif
                                        </div>
                                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700">{{ $followUp->type->name }}</span>
                                            <span>{{ $followUp->scheduled_at?->format('g:i A') }}</span>
                                        </div>
                                    </div>

                                    @if (Route::has('follow-ups.complete'))
                                        <form method="POST" action="{{ route('follow-ups.complete', $followUp) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl bg-violet-600 px-3 py-2 text-xs font-semibold text-white hover:bg-violet-700">Mark done</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-800 font-display">Recent proposals</h2>
                    <p class="mt-1 text-sm text-gray-400">Your latest submission activity.</p>
                </div>
            </div>

            @if ($recentProposals->isEmpty())
                <div class="p-6">
                    <x-empty-state icon="ti ti-file-description" title="No proposals yet" description="Create your first proposal to populate this table." />
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
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Sent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Connects</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($recentProposals as $proposal)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            @if (Route::has('proposals.show'))
                                                <a href="{{ route('proposals.show', $proposal) }}" class="hover:text-violet-700">
                                                    {{ $proposal->job?->title ?? 'Untitled job' }}
                                                </a>
                                            @else
                                                {{ $proposal->job?->title ?? 'Untitled job' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $proposal->employer?->name ?? 'Independent client' }}</td>
                                    <td class="px-6 py-4"><x-badge :status="$proposal->status" /></td>
                                    <td class="px-6 py-4"><x-score-badge :score="$proposal->ai_score" /></td>
                                    <td class="px-6 py-4 text-gray-600">{{ $proposal->sent_at?->diffForHumans() ?? '—' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $proposal->connects_spent }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if (Route::has('proposals.show'))
                                            <a href="{{ route('proposals.show', $proposal) }}" class="text-sm font-semibold text-violet-700 hover:text-violet-800">View</a>
                                        @else
                                            <span class="text-sm text-gray-400">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section>
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-medium text-gray-800 font-display">Top scored jobs</h2>
                    <p class="mt-1 text-sm text-gray-400">Jobs with the strongest proposal scores in your pipeline.</p>
                </div>
            </div>

            @if ($topScoredJobs->isEmpty())
                <x-empty-state icon="ti ti-stars" title="No high-scoring jobs yet" description="Once proposals carry strong AI scores, the best opportunities will appear here." />
            @else
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    @foreach ($topScoredJobs as $job)
                        <article class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $job->niche?->label() ?? 'Other' }}</span>
                                <x-score-badge :score="$job->max_ai_score" />
                            </div>
                            <h3 class="mt-4 font-semibold text-gray-900">
                                @if (Route::has('jobs.show'))
                                    <a href="{{ route('jobs.show', $job) }}" class="hover:text-violet-700">{{ \Illuminate\Support\Str::limit($job->title, 60) }}</a>
                                @else
                                    {{ \Illuminate\Support\Str::limit($job->title, 60) }}
                                @endif
                            </h3>
                            <p class="mt-2 text-sm text-gray-400">{{ $job->employer?->name ?? 'Independent client' }}</p>
                            @if (Route::has('jobs.show'))
                                <a href="{{ route('jobs.show', $job) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-800">
                                    <span>View job</span>
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const chartPayload = @json($chartData);
        const chartCanvas = document.getElementById('proposalChart');

        if (chartCanvas) {
            new Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: chartPayload.labels,
                    datasets: [{
                        data: chartPayload.data,
                        borderColor: '#7c3aed',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: false,
                        pointRadius: 3,
                        pointBackgroundColor: '#7c3aed',
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: '#6b7280',
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6b7280',
                            },
                            grid: {
                                color: '#e5e7eb',
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
