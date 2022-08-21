<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:first_name,dob'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }
}
