@props([
    'value' => '0',
    'label',
    'icon' => 'ti ti-chart-bar',
    'href' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'flex items-start justify-between gap-4 rounded-[1.35rem] border border-gray-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-[0_22px_44px_-34px_rgba(15,23,42,0.5)]']) }}
>
    <div>
        <div class="font-display text-3xl font-semibold tracking-[-0.04em] text-gray-900">{{ $value }}</div>
        <div class="mt-2 text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-gray-500">{{ $label }}</div>
    </div>
    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[1.15rem] border border-violet-100 bg-violet-50 text-xl text-violet-700 shadow-sm">
        <i class="{{ $icon }}"></i>
    </div>
</{{ $tag }}>
