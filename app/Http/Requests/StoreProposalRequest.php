<?php

namespace App\Http\Requests;

use App\Enums\ProposalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProposalRequest extends FormRequest
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
            'job_id' => ['required', 'exists:jobs,id'],
            'employer_id' => ['nullable', 'exists:employers,id'],
            'connects_spent' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', Rule::in(array_map(
                static fn (ProposalStatus $status) => $status->value,
                ProposalStatus::cases()
            ))],
            'bid_amount' => ['nullable', 'numeric', 'min:0'],
            'bid_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'cover_letter' => ['nullable', 'string'],
            'loom_url' => ['nullable', 'url'],
            'has_leverage' => ['nullable', 'boolean'],
            'leverage_portfolio_id' => ['nullable', 'exists:portfolios,id'],
            'leverage_notes' => ['nullable', 'string'],
        ];
    }
}
