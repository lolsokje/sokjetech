<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RaceCreateRequest extends FormRequest
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

    public function raceData(): array
    {
        $data = $this->validated();

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'circuit_id' => ['required', 'exists:circuits,id'],
            'circuit_variation_id' => ['required', 'exists:circuit_variations,id'],
            'name' => ['required'],
            'climate_id' => ['required', 'exists:climates,id'],
        ];
    }
}
