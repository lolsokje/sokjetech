<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EngineSeasonCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'base_engine_id' => ['required', 'exists:engines,id'],
            'rebadge' => ['bool'],
            'name' => ['required', 'string'],
            'individual_rating' => ['nullable', 'bool'],
        ];
    }
}
