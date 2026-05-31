@props([
    'icon' => 'ti ti-folder-open',
    'title' => 'Nothing here yet',
    'description' => 'There is no data to show right now.',
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white px-6 py-10 text-center shadow-sm']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-xl text-gray-700 shadow-sm">
        <i class="{{ $icon }}"></i>
    </div>
    <h3 class="mt-4 font-display text-lg font-medium tracking-[-0.03em] text-gray-800">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500">{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
