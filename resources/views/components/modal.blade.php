@props([
    'title' => null,
])

<div x-data="{ open: false }" {{ $attributes->merge(['class' => 'contents']) }}>
    <div @click="open = true" class="contents">
        {{ $trigger ?? '' }}
    </div>

    <template x-teleport="body">
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8">
            <div class="absolute inset-0 bg-gray-950/50 backdrop-blur-sm" @click="open = false"></div>
            <div x-show="open" x-transition class="relative z-10 w-full max-w-lg rounded-3xl border border-dashed border-gray-300 bg-white p-6 shadow-2xl" @click.outside="open = false">
                @if ($title)
                    <div class="mb-4 text-lg font-medium text-gray-800 font-display">{{ $title }}</div>
                @endif
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
