@php
    $selectedBudgetType = old('budget_type', $job?->budget_type?->value ?? \App\Enums\BudgetType::Fixed->value);
    $selectedEmployerId = old('employer_id', $job?->employer_id);
    $selectedDifficulty = old('difficulty', $job?->difficulty?->value);
    $requiredSkillsText = old('required_skills_text', collect($job?->required_skills ?? [])->implode(', '));
@endphp

<div x-data="jobFormFactory({
    budgetType: @js($selectedBudgetType),
    isFeatured: @js((bool) old('is_featured', $job?->is_featured ?? false)),
    existingUrls: {!! $existingUrlsJson !!},
    initialUrl: @js(old('url', $job?->url ?? '')),
})" class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if (! empty($method) && $method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="title" label="Job title" :value="old('title', $job?->title)" placeholder="Build AI lead qualification system" required />

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Employer</span>
                    <select name="employer_id" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Independent client</option>
                        @foreach ($employers as $employer)
                            <option value="{{ $employer->id }}" @selected($selectedEmployerId === $employer->id)>{{ $employer->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('employer_id'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('employer_id') }}</span>
                    @endif
                </label>
            </div>

            <div>
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Upwork URL</span>
                    <input type="url" name="url" x-model="jobUrl" placeholder="https://www.upwork.com/jobs/~..." class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" required>
                </label>
                @if ($errors->first('url'))
                    <span class="mt-2 block text-sm text-red-600">{{ $errors->first('url') }}</span>
                @endif
                <div x-show="urlAlreadyTracked" x-cloak class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                    This Upwork URL is already tracked in the job pipeline.
                </div>
            </div>

            <x-form-input name="description" label="Job description" type="textarea" rows="7" :value="old('description', $job?->description)" placeholder="Paste the key job details, constraints, and client goals." required />

            <div class="grid gap-5 md:grid-cols-3">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Niche</span>
                    <select name="niche" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @foreach ($niches as $niche)
                            <option value="{{ $niche->value }}" @selected(old('niche', $job?->niche?->value) === $niche->value)>{{ $niche->label() }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('niche'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('niche') }}</span>
                    @endif
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Budget type</span>
                    <select name="budget_type" x-model="budgetType" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        @foreach ($budgetTypes as $budgetType)
                            <option value="{{ $budgetType->value }}">{{ ucfirst($budgetType->value) }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('budget_type'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('budget_type') }}</span>
                    @endif
                </label>

                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Difficulty</span>
                    <select name="difficulty" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">Not set</option>
                        @foreach ($difficulties as $difficulty)
                            <option value="{{ $difficulty->value }}" @selected($selectedDifficulty === $difficulty->value)>{{ ucfirst($difficulty->value) }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('difficulty'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('difficulty') }}</span>
                    @endif
                </label>
            </div>

            <div class="grid gap-5 md:grid-cols-2" x-show="budgetType === 'fixed'" x-cloak>
                <x-form-input name="budget_min" label="Fixed budget min" type="number" step="0.01" :value="old('budget_min', $job?->budget_min)" min="0" />
                <x-form-input name="budget_max" label="Fixed budget max" type="number" step="0.01" :value="old('budget_max', $job?->budget_max)" min="0" />
            </div>

            <div class="grid gap-5 md:grid-cols-2" x-show="budgetType === 'hourly'" x-cloak>
                <x-form-input name="hourly_rate_min" label="Hourly rate min" type="number" step="0.01" :value="old('hourly_rate_min', $job?->hourly_rate_min)" min="0" />
                <x-form-input name="hourly_rate_max" label="Hourly rate max" type="number" step="0.01" :value="old('hourly_rate_max', $job?->hourly_rate_max)" min="0" />
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="posted_at" label="Posted at" type="datetime-local" :value="old('posted_at', $job?->posted_at?->format('Y-m-d\TH:i'))" />
                <x-form-input name="proposals_count_at_time" label="Proposals at post time" type="number" :value="old('proposals_count_at_time', $job?->proposals_count_at_time)" min="0" />
            </div>

            <x-form-input name="required_skills_text" label="Required skills" :value="$requiredSkillsText" placeholder="OpenAI API, Laravel, Twilio, n8n" />

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" x-model="isFeatured" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span>Pin this job as a featured opportunity</span>
            </label>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ti ti-device-floppy"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        </form>
    </section>

    <aside class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="space-y-5">
            <div>
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Pipeline guidance</div>
                <h2 class="mt-2 font-display text-lg font-medium text-gray-800">Qualify before you pitch</h2>
                <p class="mt-2 text-sm text-gray-500">Capture enough detail here to score the job, match a portfolio piece, and decide whether it deserves a proposal.</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">What gets used downstream</div>
                <div class="mt-3 space-y-3 text-sm text-gray-600">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-violet-500"></span>
                        <p>Budget, posted time, and competition feed the AI job score.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        <p>Niche and skills influence which portfolio proof points match this opportunity.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        <p>Employer assignment lets proposals inherit the client record automatically.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-dashed border-gray-300 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Preview</div>
                <div class="mt-3 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center justify-between gap-3">
                        <span>Budget mode</span>
                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700" x-text="budgetType === 'hourly' ? 'Hourly' : 'Fixed'"></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>Featured</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="isFeatured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700'" x-text="isFeatured ? 'Yes' : 'No'"></span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>URL status</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="urlAlreadyTracked ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'" x-text="urlAlreadyTracked ? 'Duplicate' : 'Clear'"></span>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            function jobFormFactory(config) {
                return {
                    budgetType: config.budgetType || 'fixed',
                    isFeatured: !!config.isFeatured,
                    jobUrl: config.initialUrl || '',
                    existingUrls: config.existingUrls || [],
                    get urlAlreadyTracked() {
                        const normalizedUrl = (this.jobUrl || '').trim().toLowerCase();

                        if (!normalizedUrl) {
                            return false;
                        }

                        return this.existingUrls.map(url => url.toLowerCase()).includes(normalizedUrl);
                    },
                };
            }
        </script>
    @endpush
@endonce
