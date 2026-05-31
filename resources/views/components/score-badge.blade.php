@props(['score' => null, 'large' => false])

@php
    $scoreValue = $score === null || $score === '' ? null : (int) $score;
    $classes = match (true) {
        $scoreValue === null => 'border border-slate-200 bg-slate-100 text-slate-500',
        $scoreValue <= 4 => 'border border-rose-200 bg-rose-50 text-rose-700',
        $scoreValue <= 6 => 'border border-amber-200 bg-amber-50 text-amber-700',
        $scoreValue <= 8 => 'border border-sky-200 bg-sky-50 text-sky-700',
        default => 'border border-emerald-200 bg-emerald-50 text-emerald-700',
    };
    $sizeClasses = $large ? 'px-4 py-2 text-base font-semibold' : 'px-2.5 py-1 text-xs font-semibold';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full tabular-nums {$classes} {$sizeClasses}"]) }}>
    {{ $scoreValue ?? '—' }}
</span>
