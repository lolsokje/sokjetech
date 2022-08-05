<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EngineReliabilityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'engines' => ['required', 'array'],
            'engines.*.id' => ['required', 'exists:engine_seasons,id'],
            'engines.*.new' => ['required', 'integer', 'min:1'],
        ];
    }
}
