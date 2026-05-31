<?php

namespace App\Http\Requests;

use App\Enums\FollowUpType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFollowUpRequest extends FormRequest
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
            'proposal_id' => ['required', 'exists:proposals,id'],
            'type' => ['required', Rule::in(array_map(
                static fn (FollowUpType $type) => $type->value,
                FollowUpType::cases()
            ))],
            'scheduled_at' => ['required', 'date'],
            'outcome_note' => ['nullable', 'string'],
        ];
    }
}
