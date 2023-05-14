<?php

namespace App\Http\Requests;

use App\Rules\StintRngValid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

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
        unset($data['stints']);

        return $data;
    }

    public function stints(): Collection
    {
        return collect($this->safe(['stints'])['stints']);
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
            'name' => ['required'],
            'stints' => ['required', new StintRngValid],
            'climate_id' => ['required', 'exists:climates,id'],
        ];
    }
}
