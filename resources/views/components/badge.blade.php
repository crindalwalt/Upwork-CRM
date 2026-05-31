@props(['status'])

@php
    $resolved = $status instanceof \App\Enums\ProposalStatus ? $status : \App\Enums\ProposalStatus::tryFrom((string) $status);
    $classes = match ($resolved?->value) {
        \App\Enums\ProposalStatus::Won->value => 'bg-emerald-100 text-emerald-800',
        \App\Enums\ProposalStatus::Viewed->value => 'bg-amber-100 text-amber-800',
        \App\Enums\ProposalStatus::Lost->value => 'bg-red-100 text-red-800',
        \App\Enums\ProposalStatus::Sent->value => 'bg-blue-100 text-blue-800',
        \App\Enums\ProposalStatus::Replied->value => 'bg-purple-100 text-purple-800',
        \App\Enums\ProposalStatus::InterviewScheduled->value => 'bg-slate-100 text-slate-800',
        \App\Enums\ProposalStatus::Withdrawn->value => 'bg-orange-100 text-orange-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ $resolved?->label() ?? \Illuminate\Support\Str::of((string) $status)->headline() }}
</span>
