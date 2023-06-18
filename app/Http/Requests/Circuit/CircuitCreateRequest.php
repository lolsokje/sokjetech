<?php

namespace App\Http\Requests\Circuit;

use App\Rules\ValidLaptimeRule;
use Illuminate\Foundation\Http\FormRequest;

class CircuitCreateRequest extends FormRequest
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
            'name' => ['required'],
            'country' => ['required'],
            'default_climate_id' => ['required', 'exists:climates,id'],
            'shared' => ['nullable', 'boolean'],
            'length' => ['required', 'numeric', 'min:1'],
            'base_laptime' => ['required', new ValidLaptimeRule],
        ];
    }

    public function circuitData(): array
    {
        return $this->only([
            'name',
            'country',
            'default_climate_id',
            'shared',
        ]);
    }

    public function variationData(): array
    {
        return $this->only([
            'name',
            'length',
            'base_laptime',
        ]);
    }
}
