<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortfolioRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'loom_url' => ['nullable', 'url'],
            'live_url' => ['nullable', 'url'],
            'github_url' => ['nullable', 'url'],
            'tags_text' => ['nullable', 'string'],
            'tech_stack_text' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string'],
            'client_location' => ['nullable', 'string'],
            'outcome_summary' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
