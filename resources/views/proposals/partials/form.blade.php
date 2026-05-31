@php
    $selectedJobId = old('job_id', $proposal?->job_id);
    $selectedEmployerId = old('employer_id', $proposal?->employer_id);
    $selectedStatus = old('status', $proposal?->status?->value ?? \App\Enums\ProposalStatus::Draft->value);
    $hasLeverage = (bool) old('has_leverage', $proposal?->has_leverage ?? false);
@endphp

<div x-data="proposalFormFactory({
    jobs: {!! $jobsJson !!},
    jobScores: {!! $jobScoresJson !!},
    portfolioMatches: {!! $portfolioMatchesJson !!},
    selectedJobId: @js($selectedJobId),
    selectedEmployerId: @js($selectedEmployerId),
    selectedStatus: @js($selectedStatus),
    initialHasLeverage: @js($hasLeverage),
    initialSearch: @js(optional($jobs->firstWhere('id', $selectedJobId))->title),
})" x-init="init()" class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
    <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if (! empty($method) && $method !== 'POST')
                @method($method)
            @endif

            <input type="hidden" name="job_id" x-model="selectedJobId">

            <div class="relative">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Job selector</span>
                    <input type="text" x-model="searchTerm" @focus="openJobDropdown = true" @input="openJobDropdown = true" placeholder="Search jobs by title..." class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </label>
                @if ($errors->first('job_id'))
                    <span class="mt-2 block text-sm text-red-600">{{ $errors->first('job_id') }}</span>
                @endif

                <div x-show="openJobDropdown" x-cloak @click.outside="openJobDropdown = false" class="absolute z-20 mt-2 max-h-72 w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white p-2 shadow-2xl">
                    <template x-for="job in filteredJobs" :key="job.id">
                        <button type="button" @click="selectJob(job)" class="flex w-full items-start justify-between gap-3 rounded-xl px-3 py-3 text-left hover:bg-gray-50">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900" x-text="job.title"></span>
                                <span class="mt-1 block text-xs text-gray-400" x-text="job.employer_name || 'Independent client'"></span>
                            </span>
                            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700" x-text="job.niche || 'Other'"></span>
                        </button>
                    </template>
                    <div x-show="filteredJobs.length === 0" class="px-3 py-4 text-sm text-gray-400">No jobs match your search.</div>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Employer</span>
                    <select name="employer_id" x-model="selectedEmployerId" @change="employerManuallyChanged = true" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Auto-fill from job</option>
                        @foreach ($employers as $employer)
                            <option value="{{ $employer->id }}">{{ $employer->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('employer_id'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('employer_id') }}</span>
                    @endif
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Status</span>
                    <select name="status" x-model="selectedStatus" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('status'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('status') }}</span>
                    @endif
                </label>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="connects_spent" label="Connects spent" type="number" :value="old('connects_spent', $proposal?->connects_spent ?? 8)" min="1" max="20" required />

                <template x-if="selectedBudgetType === 'hourly'">
                    <div>
                        <x-form-input name="bid_hourly_rate" label="Bid hourly rate" type="number" step="0.01" :value="old('bid_hourly_rate', $proposal?->bid_hourly_rate)" placeholder="45.00" />
                    </div>
                </template>

                <template x-if="selectedBudgetType !== 'hourly'">
                    <div>
                        <x-form-input name="bid_amount" label="Bid amount" type="number" step="0.01" :value="old('bid_amount', $proposal?->bid_amount)" placeholder="1200.00" />
                    </div>
                </template>
            </div>

            <x-form-input name="cover_letter" label="Cover letter" type="textarea" :value="old('cover_letter', $proposal?->cover_letter)" rows="4" placeholder="The 4-line text proposal that accompanies your Loom..." />
            <x-form-input name="loom_url" label="Loom URL" type="url" :value="old('loom_url', $proposal?->loom_url)" placeholder="https://www.loom.com/share/..." />

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <input type="checkbox" name="has_leverage" value="1" x-model="hasLeverage" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span>Has leverage from an existing portfolio piece</span>
            </label>

            <div x-show="hasLeverage" x-cloak class="space-y-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Portfolio selector</span>
                    <select name="leverage_portfolio_id" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Select a portfolio piece</option>
                        @foreach ($portfolios as $portfolio)
                            <option value="{{ $portfolio->id }}" @selected(old('leverage_portfolio_id', $proposal?->leverage_portfolio_id) === $portfolio->id)>
                                {{ $portfolio->title }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->first('leverage_portfolio_id'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('leverage_portfolio_id') }}</span>
                    @endif
                </label>

                <x-form-input name="leverage_notes" label="Leverage notes" type="textarea" :value="old('leverage_notes', $proposal?->leverage_notes)" rows="3" placeholder="How this portfolio piece connects to the job..." />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ti ti-device-floppy"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        </form>
    </section>

    <aside class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <template x-if="selectedJob">
            <div class="space-y-5">
                <div>
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Job preview</div>
                    <h2 class="mt-2 text-lg font-medium text-gray-800 font-display" x-text="selectedJob.title"></h2>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-violet-50 px-2.5 py-1 font-semibold text-violet-700" x-text="selectedJob.niche || 'Other'"></span>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700" x-text="selectedJob.budget_display"></span>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Posted</div>
                        <div class="mt-2 text-sm text-gray-700" x-text="selectedJob.posted_at || 'Unknown'"></div>
                    </div>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Competition</div>
                        <div class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="(selectedJob.proposals_count_at_time || 0) > 20 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700'" x-text="(selectedJob.proposals_count_at_time ?? 0) + ' proposals at time'"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Employer</div>
                    <div class="mt-3 text-sm font-semibold text-gray-900" x-text="selectedJob.employer?.name || 'Independent client'"></div>
                    <div class="mt-2 space-y-1 text-sm text-gray-500">
                        <div>Hire rate: <span class="text-gray-700" x-text="selectedJob.employer?.hire_rate ? selectedJob.employer.hire_rate + '%' : '—'"></span></div>
                        <div>Total spent: <span class="text-gray-700" x-text="selectedJob.employer?.total_spent ? '$' + selectedJob.employer.total_spent : '—'"></span></div>
                        <div>
                            Payment verified:
                            <span class="font-semibold" :class="selectedJob.employer?.payment_verified ? 'text-emerald-700' : 'text-red-600'" x-text="selectedJob.employer?.payment_verified ? 'Yes' : 'No'"></span>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-1">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">AI score</div>
                        <div class="mt-3 flex items-center gap-3">
                            <span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-800" x-text="selectedScore?.score ?? '—'"></span>
                            <p class="text-sm text-gray-500" x-text="selectedScore?.reasoning || 'No score available yet.'"></p>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Best portfolio match</div>
                        <template x-if="selectedMatch">
                            <div class="mt-3 rounded-2xl border border-violet-100 bg-violet-50 p-4">
                                <div class="text-sm font-semibold text-violet-900" x-text="selectedMatch.title"></div>
                                <p class="mt-2 text-sm text-violet-700" x-text="selectedMatch.outcome_summary || 'Strong existing proof point for this job.'"></p>
                            </div>
                        </template>
                        <template x-if="!selectedMatch">
                            <p class="mt-3 text-sm text-gray-400">No matched portfolio piece yet.</p>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="!selectedJob">
            <div class="py-12">
                <x-empty-state icon="ti ti-briefcase-off" title="No job selected" description="Choose a job to preview the client profile, AI score, and best leverage match." />
            </div>
        </template>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            function proposalFormFactory(config) {
                return {
                    jobs: config.jobs,
                    jobScores: config.jobScores,
                    portfolioMatches: config.portfolioMatches,
                    selectedJobId: config.selectedJobId || '',
                    selectedEmployerId: config.selectedEmployerId || '',
                    selectedStatus: config.selectedStatus || 'draft',
                    selectedBudgetType: 'fixed',
                    hasLeverage: !!config.initialHasLeverage,
                    searchTerm: config.initialSearch || '',
                    openJobDropdown: false,
                    employerManuallyChanged: false,
                    init() {
                        if (this.selectedJob) {
                            this.selectedBudgetType = this.selectedJob.budget_type || 'fixed';

                            if (!this.selectedEmployerId) {
                                this.selectedEmployerId = this.selectedJob.employer_id || '';
                            }
                        }
                    },
                    get filteredJobs() {
                        const term = (this.searchTerm || '').toLowerCase();

                        return this.jobs
                            .filter(job => !term || job.title.toLowerCase().includes(term) || (job.employer_name || '').toLowerCase().includes(term))
                            .slice(0, 8);
                    },
                    get selectedJob() {
                        return this.jobs.find(job => job.id === this.selectedJobId) || null;
                    },
                    get selectedScore() {
                        return this.selectedJobId ? this.jobScores[this.selectedJobId] : null;
                    },
                    get selectedMatch() {
                        return this.selectedJobId ? this.portfolioMatches[this.selectedJobId] : null;
                    },
                    selectJob(job) {
                        this.selectedJobId = job.id;
                        this.searchTerm = job.title;
                        this.selectedBudgetType = job.budget_type || 'fixed';

                        if (!this.employerManuallyChanged) {
                            this.selectedEmployerId = job.employer_id || '';
                        }

                        this.openJobDropdown = false;
                    },
                };
            }
        </script>
    @endpush
@endonce
