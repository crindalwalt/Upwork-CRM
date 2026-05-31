@props(['score' => null, 'large' => false])

@php
    $scoreValue = $score === null || $score === '' ? null : (int) $score;
    $classes = match (true) {
        $scoreValue === null => 'bg-gray-100 text-gray-500',
        $scoreValue <= 4 => 'bg-red-100 text-red-800',
        $scoreValue <= 6 => 'bg-amber-100 text-amber-800',
        $scoreValue <= 8 => 'bg-blue-100 text-blue-800',
        default => 'bg-emerald-100 text-emerald-800',
    };
    $sizeClasses = $large ? 'px-4 py-2 text-base font-semibold' : 'px-2.5 py-1 text-xs font-semibold';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full {$classes} {$sizeClasses}"]) }}>
    {{ $scoreValue ?? '—' }}
</span>
