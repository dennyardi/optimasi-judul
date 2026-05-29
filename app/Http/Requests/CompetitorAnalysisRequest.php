<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitorAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel_input' => ['required', 'string', 'min:2', 'max:220'],
            'range' => ['required', Rule::in(['1_month', '3_months', '6_months', '1_year'])],
        ];
    }
}
