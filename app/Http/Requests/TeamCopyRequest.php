<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamCopyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'universe_id' => ['required', 'exists:universes,id'],
        ];
    }
}
