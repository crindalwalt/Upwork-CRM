@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'autocomplete' => null,
    'required' => false,
    'textarea' => false,
    'rows' => 4,
    'min' => null,
    'max' => null,
    'step' => null,
])

@php
    $fieldId = str_replace(['[', ']'], ['_', ''], $name);
    $error = $errors->first($name);
    $baseClasses = 'mt-2 block w-full rounded-xl border bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:outline-none focus:ring-2';
    $stateClasses = $error ? 'border-red-300 focus:border-red-400 focus:ring-red-200' : 'border-gray-200 focus:border-violet-400 focus:ring-violet-200';
@endphp

<label for="{{ $fieldId }}" class="block">
    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</span>

    @if ($textarea || $type === 'textarea')
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses}"]) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            autocomplete="{{ $autocomplete }}"
            @if($required) required @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($step !== null) step="{{ $step }}" @endif
            {{ $attributes->merge(['class' => "{$baseClasses} {$stateClasses}"]) }}
        >
    @endif

    @if ($error)
        <span class="mt-2 block text-sm text-red-600">{{ $error }}</span>
    @endif
</label>
