<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:full_name,short_name,team_principal,country'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }
}
