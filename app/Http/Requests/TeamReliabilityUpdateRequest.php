<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamReliabilityUpdateRequest extends FormRequest
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
            'teams' => ['required', 'array'],
            'teams.*.id' => ['required', 'exists:entrants,id'],
            'teams.*.new' => ['required', 'integer', 'min:1'],
        ];
    }
}
