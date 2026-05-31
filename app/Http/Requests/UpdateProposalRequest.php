<?php

namespace App\Http\Requests;

use App\Enums\ProposalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProposalRequest extends FormRequest
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
            'job_id' => ['sometimes', 'exists:jobs,id'],
            'connects_spent' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'status' => ['sometimes', Rule::in(array_map(
                static fn (ProposalStatus $status) => $status->value,
                ProposalStatus::cases()
            ))],
            'bid_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'bid_hourly_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'cover_letter' => ['sometimes', 'nullable', 'string'],
            'loom_url' => ['sometimes', 'nullable', 'url'],
            'has_leverage' => ['sometimes', 'boolean'],
            'leverage_portfolio_id' => ['sometimes', 'nullable', 'exists:portfolios,id'],
            'leverage_notes' => ['sometimes', 'nullable', 'string'],
            'ai_score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
            'ai_score_reasoning' => ['sometimes', 'nullable', 'string'],
            'ai_script' => ['sometimes', 'nullable', 'string'],
            'loss_reason' => ['sometimes', 'nullable', 'string', 'max:255'],
            'won_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
