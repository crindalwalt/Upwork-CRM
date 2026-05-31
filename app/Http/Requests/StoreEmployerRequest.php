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
            'payment_verified' => ['nullable', 'boolean'],
            'flag' => ['nullable', Rule::in(['green', 'yellow', 'red'])],
        ];
    }
}
