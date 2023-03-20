<?php

namespace App\Http\Requests;

class DriverFilterRequest extends FilterRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:first_name,dob,retired'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function field(): string
    {
        return $this->validated('field') ?? 'first_name';
    }
}
