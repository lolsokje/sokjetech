<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LineupCreateRequest extends FormRequest
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
            'entrant_id' => ['required', 'exists:entrants,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'number' => ['required', 'integer'],
        ];
    }
}
