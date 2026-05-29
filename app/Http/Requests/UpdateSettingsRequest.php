<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public const MODELS = [
        'gpt-4.1',
        'gpt-4.1-mini',
        'gpt-4o',
        'gpt-4o-mini',
        'gpt-5',
        'gpt-5-mini',
        'gpt-5-nano',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'openai_api_key' => ['nullable', 'string', 'min:20', 'max:300'],
            'youtube_api_key' => ['nullable', 'string', 'min:20', 'max:300'],
            'ai_model' => ['required', Rule::in(self::MODELS)],
            'auto_capslock_titles' => ['nullable', 'boolean'],
            'description_style' => ['required', Rule::in(['long', 'short'])],
            'channel_name' => ['nullable', 'string', 'max:120'],
            'niche' => ['nullable', 'string', 'max:160'],
            'tone' => ['nullable', 'string', 'max:160'],
            'language' => ['nullable', 'string', 'max:80'],
            'default_cta' => ['nullable', 'string', 'max:500'],
        ];
    }
}
