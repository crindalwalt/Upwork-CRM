@props([
    'value' => '0',
    'label',
    'icon' => 'ti ti-chart-bar',
    'href' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl p-5 flex items-start justify-between gap-4 shadow-sm']) }}
>
    <div>
        <div class="text-3xl font-semibold text-gray-900 font-display">{{ $value }}</div>
        <div class="mt-2 text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</div>
    </div>
    <div class="h-11 w-11 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl shrink-0">
        <i class="{{ $icon }}"></i>
    </div>
</{{ $tag }}>
