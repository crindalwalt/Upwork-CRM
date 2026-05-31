@props([
    'icon' => 'ti ti-folder-open',
    'title' => 'Nothing here yet',
    'description' => 'There is no data to show right now.',
])

<div {{ $attributes->merge(['class' => 'rounded-[1.6rem] border border-dashed border-gray-300 bg-[linear-gradient(180deg,rgba(255,255,255,0.98),rgba(248,250,252,0.96))] px-6 py-12 text-center shadow-sm']) }}>
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.3rem] border border-violet-100 bg-[linear-gradient(135deg,rgba(220,231,251,0.95),rgba(242,231,216,0.92))] text-3xl text-violet-700 shadow-sm">
        <i class="{{ $icon }}"></i>
    </div>
    <h3 class="mt-4 font-display text-xl font-medium tracking-[-0.03em] text-gray-800">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
