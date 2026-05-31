<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployerRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'upwork_url' => ['nullable', 'url'],
            'location' => ['nullable', 'string'],
            'total_spent' => ['nullable', 'numeric', 'min:0'],
            'hire_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'reviews_count' => ['nullable', 'integer', 'min:0'],
            'payment_verified' => ['nullable', 'boolean'],
            'open_jobs_count' => ['nullable', 'integer', 'min:0'],
            'member_since' => ['nullable', 'date'],
            'internal_notes' => ['nullable', 'string'],
            'flag' => ['nullable', Rule::in(['green', 'yellow', 'red'])],
        ];
    }
}
