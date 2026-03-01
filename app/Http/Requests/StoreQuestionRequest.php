<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'question_text' => ['required','string','max:500'],
            'type' => ['required','in:mcq,text'],
            'is_required' => ['nullable','boolean'],
            'options_raw' => ['nullable','string'],
            'sort_order' => ['nullable','integer','min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                if ($this->input('type') !== 'mcq') {
                    return;
                }

                $options = array_values(array_filter(array_map('trim', explode('|', (string) $this->input('options_raw')))));

                if (count($options) < 2) {
                    $validator->errors()->add('options_raw', __('app.validation.mcq_options'));
                }
            },
        ];
    }
}
