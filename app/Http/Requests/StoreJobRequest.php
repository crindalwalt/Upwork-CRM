<?php

namespace App\Http\Requests;

use App\Enums\BudgetType;
use App\Enums\JobNiche;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'url' => ['required', 'url', Rule::unique('jobs', 'url')],
            'niche' => ['required', Rule::in(array_map(
                static fn (JobNiche $niche) => $niche->value,
                JobNiche::cases()
            ))],
            'budget_type' => ['required', Rule::in(array_map(
                static fn (BudgetType $type) => $type->value,
                BudgetType::cases()
            ))],
            'description' => ['nullable', 'string'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_min' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate_max' => ['nullable', 'numeric', 'min:0'],
            'posted_at' => ['nullable', 'date'],
            'proposals_count_at_time' => ['nullable', 'integer', 'min:0'],
            'employer_id' => ['nullable', 'exists:employers,id'],
        ];
    }
}
