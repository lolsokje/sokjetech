<?php

namespace App\Http\Requests;

use App\Enums\QualifyingFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class QualifyingConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'selected_format' => ['required', new Enum(QualifyingFormat::class)],
            'format_details' => ['required', 'array'],
        ];
    }
}
