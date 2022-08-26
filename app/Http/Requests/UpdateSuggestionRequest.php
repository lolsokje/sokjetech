<?php

namespace App\Http\Requests;

use App\Enums\SuggestionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(SuggestionStatus::class)],
            'admin_remarks' => ['nullable'],
        ];
    }
}
