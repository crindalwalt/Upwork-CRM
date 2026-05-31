@php
    $tagsText = old('tags_text', collect($portfolio?->tags ?? [])->implode(', '));
    $techStackText = old('tech_stack_text', collect($portfolio?->tech_stack ?? [])->implode(', '));
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(320px,2fr)]">
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if (! empty($method) && $method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="title" label="Case study title" :value="old('title', $portfolio?->title)" required />
                <x-form-input name="client_name" label="Client name" :value="old('client_name', $portfolio?->client_name)" />
            </div>

            <x-form-input name="description" label="Description" type="textarea" rows="7" :value="old('description', $portfolio?->description)" placeholder="Summarize the problem, scope, and delivery." required />

            <div class="grid gap-5 md:grid-cols-3">
                <x-form-input name="loom_url" label="Loom URL" type="url" :value="old('loom_url', $portfolio?->loom_url)" />
                <x-form-input name="live_url" label="Live URL" type="url" :value="old('live_url', $portfolio?->live_url)" />
                <x-form-input name="github_url" label="GitHub URL" type="url" :value="old('github_url', $portfolio?->github_url)" />
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="client_location" label="Client location" :value="old('client_location', $portfolio?->client_location)" />
                <x-form-input name="sort_order" label="Sort order" type="number" :value="old('sort_order', $portfolio?->sort_order ?? 0)" min="0" />
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-form-input name="tags_text" label="Tags" :value="$tagsText" placeholder="ai_agent, automation, n8n" />
                <x-form-input name="tech_stack_text" label="Tech stack" :value="$techStackText" placeholder="Laravel, OpenAI API, Twilio" />
            </div>

            <x-form-input name="outcome_summary" label="Outcome summary" type="textarea" rows="4" :value="old('outcome_summary', $portfolio?->outcome_summary)" placeholder="A concise result statement you can reuse in proposals." />

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $portfolio?->is_featured ?? false)) class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span>Mark this as a featured proof point</span>
            </label>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700">
                <i class="ti ti-device-floppy"></i>
                <span>{{ $submitLabel }}</span>
            </button>
        </form>
    </section>

    <aside class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div>
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Leverage strategy</div>
            <h2 class="mt-2 font-display text-lg font-medium text-gray-800">Make proof easy to reuse</h2>
            <p class="mt-2 text-sm text-gray-500">The tags here are what the proposal form uses to surface the best case study for a matching job niche.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
            <div class="font-semibold text-gray-800">Best practice</div>
            <div class="mt-3 space-y-2">
                <p>Keep tags short and niche-oriented, like `automation`, `voice_ai`, or `chatbot`.</p>
                <p>Use the outcome summary as the proposal-ready one-liner you want to quote most often.</p>
                <p>Feature only the strongest proof points so they rise to the top when time is tight.</p>
            </div>
        </div>
    </aside>
</div>
