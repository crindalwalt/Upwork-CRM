<div class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if (! empty($method) && $method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="name" label="Employer name" :value="old('name', $employer?->name)" required />
                <x-form-input name="upwork_url" label="Upwork URL" type="url" :value="old('upwork_url', $employer?->upwork_url)" placeholder="https://www.upwork.com/companies/~..." />
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="location" label="Location" :value="old('location', $employer?->location)" />
                <x-form-input name="member_since" label="Member since" type="date" :value="old('member_since', $employer?->member_since?->format('Y-m-d'))" />
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <x-form-input name="total_spent" label="Total spent" type="number" step="0.01" :value="old('total_spent', $employer?->total_spent)" min="0" />
                <x-form-input name="hire_rate" label="Hire rate %" type="number" step="0.01" :value="old('hire_rate', $employer?->hire_rate)" min="0" max="100" />
                <x-form-input name="reviews_count" label="Reviews" type="number" :value="old('reviews_count', $employer?->reviews_count)" min="0" />
                <x-form-input name="open_jobs_count" label="Open jobs" type="number" :value="old('open_jobs_count', $employer?->open_jobs_count)" min="0" />
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Flag</span>
                    <select name="flag" class="mt-2 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                        <option value="">No flag</option>
                        @foreach (['green' => 'Green', 'yellow' => 'Yellow', 'red' => 'Red'] as $flagValue => $flagLabel)
                            <option value="{{ $flagValue }}" @selected(old('flag', $employer?->flag) === $flagValue)>{{ $flagLabel }}</option>
                        @endforeach
                    </select>
                    @if ($errors->first('flag'))
                        <span class="mt-2 block text-sm text-red-600">{{ $errors->first('flag') }}</span>
                    @endif
                </label>

                <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 md:mt-7">
                    <input type="hidden" name="payment_verified" value="0">
                    <input type="checkbox" name="payment_verified" value="1" @checked(old('payment_verified', $employer?->payment_verified ?? false)) class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <span>Payment method is verified</span>
                </label>
            </div>

            <x-form-input name="internal_notes" label="Internal notes" type="textarea" rows="5" :value="old('internal_notes', $employer?->internal_notes)" placeholder="Private notes about client behavior, trust, and positioning." />

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ti ti-device-floppy"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        </form>
    </section>

    <aside class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div>
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Why this record matters</div>
            <h2 class="mt-2 font-display text-lg font-medium text-gray-800">Client quality compounds</h2>
            <p class="mt-2 text-sm text-gray-500">A strong employer profile makes future scoring and proposal decisions faster because the trust signals are already captured.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
            <div class="font-semibold text-gray-800">Recommended fields</div>
            <div class="mt-3 space-y-2">
                <p>Hire rate and spend history drive quality scoring.</p>
                <p>Flags help you avoid low-trust clients without re-evaluating from scratch.</p>
                <p>Notes preserve context that otherwise gets lost between proposal cycles.</p>
            </div>
        </div>
    </aside>
</div>
