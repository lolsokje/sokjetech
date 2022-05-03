<?php

namespace App\Http\Requests;

use App\Rules\StintRngValid;
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
            'stints' => ['required', new StintRngValid()],
        ];
    }
}
