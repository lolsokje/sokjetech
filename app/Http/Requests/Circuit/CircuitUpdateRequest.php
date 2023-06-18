<?php

namespace App\Http\Requests\Circuit;

use Illuminate\Foundation\Http\FormRequest;

class CircuitUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'country' => ['required'],
            'default_climate_id' => ['required', 'exists:climates,id'],
            'shared' => ['nullable', 'boolean'],
        ];
    }
}
