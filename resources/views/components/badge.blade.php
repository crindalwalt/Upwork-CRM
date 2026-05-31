@props(['status'])

@php
    $resolved = $status instanceof \App\Enums\ProposalStatus ? $status : \App\Enums\ProposalStatus::tryFrom((string) $status);
    $classes = match ($resolved?->value) {
        \App\Enums\ProposalStatus::Won->value => 'border border-gray-300 bg-gray-100 text-gray-800',
        \App\Enums\ProposalStatus::Replied->value => 'border border-gray-300 bg-gray-100 text-gray-800',
        \App\Enums\ProposalStatus::Lost->value => 'border border-gray-200 bg-white text-gray-500',
        \App\Enums\ProposalStatus::Withdrawn->value => 'border border-gray-200 bg-white text-gray-500',
        default => 'border border-gray-200 bg-gray-50 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
    {{ $resolved?->label() ?? \Illuminate\Support\Str::of((string) $status)->headline() }}
</span>
