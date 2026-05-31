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
            <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-md" @click="open = false"></div>
            <div x-show="open" x-transition.scale.opacity.duration.200ms class="relative z-10 w-full max-w-md rounded-[1.75rem] border border-white/60 bg-[rgba(255,255,255,0.88)] p-6 shadow-[0_32px_90px_-45px_rgba(15,23,42,0.48)] backdrop-blur-xl" @click.outside="open = false">
                <div class="flex items-start gap-4">
                    <div class="mt-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-[1.15rem] border border-red-100 bg-red-50 text-red-700 shadow-sm">
                        <i class="ti ti-trash text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-xl font-medium tracking-[-0.03em] text-gray-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="rounded-xl border border-gray-200 bg-white/80 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">Cancel</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-red-200 bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">{{ $confirmLabel }}</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
