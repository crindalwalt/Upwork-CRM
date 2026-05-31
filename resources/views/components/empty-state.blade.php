@props([
    'icon' => 'ti ti-folder-open',
    'title' => 'Nothing here yet',
    'description' => 'There is no data to show right now.',
])

<div {{ $attributes->merge(['class' => 'bg-white border border-dashed border-gray-300 rounded-2xl px-6 py-12 text-center']) }}>
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-3xl text-gray-400">
        <i class="{{ $icon }}"></i>
    </div>
    <h3 class="mt-4 text-lg font-medium text-gray-800 font-display">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm text-gray-400">{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
