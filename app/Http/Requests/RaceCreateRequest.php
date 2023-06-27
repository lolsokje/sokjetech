<?php

namespace App\Http\Requests;

use App\Enums\DistanceType;
use App\Enums\RaceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'race_type' => ['required', new Enum(RaceType::class)],
            'duration' => ['required', 'numeric', 'min:1'],
            'distance_type' => ['nullable', new Enum(DistanceType::class)],
        ];
    }
}
