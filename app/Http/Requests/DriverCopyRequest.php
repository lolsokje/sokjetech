<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverCopyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'universe_id' => ['required', 'exists:universes,id'],
        ];
    }
}
