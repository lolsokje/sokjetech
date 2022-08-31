<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrantCreateRequest extends FormRequest
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
            'team_id' => ['required', 'exists:teams,id'],
            'engine_id' => ['nullable', 'exists:engine_seasons,id'],
            'full_name' => ['required'],
            'short_name' => ['required'],
            'team_principal' => ['required'],
            'primary_colour' => ['required'],
            'secondary_colour' => ['required'],
            'accent_colour' => ['required'],
            'country' => ['required'],
        ];
    }
}
