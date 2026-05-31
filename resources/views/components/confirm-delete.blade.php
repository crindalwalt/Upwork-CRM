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
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8">
            <div class="absolute inset-0 bg-gray-950/50 backdrop-blur-sm" @click="open = false"></div>
            <div x-show="open" x-transition class="relative z-10 w-full max-w-md rounded-3xl border border-dashed border-gray-300 bg-white p-6 shadow-2xl" @click.outside="open = false">
                <div class="flex items-start gap-4">
                    <div class="mt-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-700">
                        <i class="ti ti-trash text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 font-display">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">{{ $confirmLabel }}</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
