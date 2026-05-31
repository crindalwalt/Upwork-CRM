@props(['score' => null, 'large' => false])

@php
    $scoreValue = $score === null || $score === '' ? null : (int) $score;
    $classes = match (true) {
        $scoreValue === null => 'border border-gray-200 bg-white text-gray-400',
        $scoreValue <= 4 => 'border border-gray-200 bg-white text-gray-500',
        $scoreValue <= 6 => 'border border-gray-200 bg-gray-50 text-gray-600',
        $scoreValue <= 8 => 'border border-gray-300 bg-gray-100 text-gray-700',
        default => 'border border-gray-900 bg-gray-900 text-white',
    };
    $sizeClasses = $large ? 'px-4 py-2 text-base font-semibold' : 'px-2.5 py-1 text-xs font-semibold';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full tabular-nums {$classes} {$sizeClasses}"]) }}>
    {{ $scoreValue ?? '—' }}
</span>
