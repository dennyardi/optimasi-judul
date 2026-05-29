<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_title' => ['required', 'string', 'min:4', 'max:180'],
            'short_description' => ['required', 'string', 'min:12', 'max:2000'],
        ];
    }
}
