@props([
    'action',
    'title' => 'Delete item',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Delete',
])

<div x-data="{ open: false }" class="contents">
    <button type="button" @click="open = true" class="{{ $attributes->get('class') }}">
        {{ $slot }}
    </button>

    <template x-teleport="body">
        <div x-cloak x-show="open" x-transition.opacity.duration.200ms class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8">
            <div class="absolute inset-0 bg-slate-950/30 backdrop-blur-sm" @click="open = false"></div>
            <div x-show="open" x-transition.scale.opacity.duration.200ms class="relative z-10 w-full max-w-md rounded-2xl border border-gray-200 bg-white p-5 shadow-[0_24px_60px_-36px_rgba(15,23,42,0.32)]" @click.outside="open = false">
                <div class="flex items-start gap-4">
                    <div class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-900 text-white shadow-sm">
                        <i class="ti ti-trash text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-medium tracking-[-0.03em] text-gray-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">Cancel</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-gray-900 bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-black">{{ $confirmLabel }}</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
