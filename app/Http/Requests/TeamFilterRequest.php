<?php

namespace App\Http\Requests;

class TeamFilterRequest extends FilterRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:full_name,short_name,team_principal,country'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function field(): string
    {
        return $this->validated('field') ?? 'full_name';
    }
}
