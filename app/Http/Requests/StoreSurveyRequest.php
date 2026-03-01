<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date', 'after:published_at'],
            'duplicate_policy' => ['required', 'in:user_only,ip_only,cookie_only,none'],
        ];
    }
}
