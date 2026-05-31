@props([
    'value' => '0',
    'label',
    'icon' => 'ti ti-chart-bar',
    'href' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'flex items-start justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition duration-200 hover:border-violet-200']) }}
>
    <div>
        <div class="font-display text-2xl font-semibold tracking-[-0.04em] text-gray-900">{{ $value }}</div>
        <div class="mt-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-gray-500">{{ $label }}</div>
    </div>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 text-lg text-gray-700 shadow-sm">
        <i class="{{ $icon }}"></i>
    </div>
</{{ $tag }}>
