@props(['status'])

@php
    $resolved = $status instanceof \App\Enums\ProposalStatus ? $status : \App\Enums\ProposalStatus::tryFrom((string) $status);
    $classes = match ($resolved?->value) {
        \App\Enums\ProposalStatus::Won->value => 'border border-emerald-200 bg-emerald-50 text-emerald-700',
        \App\Enums\ProposalStatus::Viewed->value => 'border border-amber-200 bg-amber-50 text-amber-700',
        \App\Enums\ProposalStatus::Lost->value => 'border border-rose-200 bg-rose-50 text-rose-700',
        \App\Enums\ProposalStatus::Sent->value => 'border border-sky-200 bg-sky-50 text-sky-700',
        \App\Enums\ProposalStatus::Replied->value => 'border border-indigo-200 bg-indigo-50 text-indigo-700',
        \App\Enums\ProposalStatus::InterviewScheduled->value => 'border border-slate-200 bg-slate-100 text-slate-700',
        \App\Enums\ProposalStatus::Withdrawn->value => 'border border-stone-200 bg-stone-100 text-stone-700',
        default => 'border border-slate-200 bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-60"></span>
    {{ $resolved?->label() ?? \Illuminate\Support\Str::of((string) $status)->headline() }}
</span>
