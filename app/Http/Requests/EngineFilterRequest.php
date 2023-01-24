<?php

namespace App\Http\Requests;

class EngineFilterRequest extends FilterRequest
{
    public function rules(): array
    {
        return [
            'field' => ['nullable', 'in:name'],
            'direction' => ['nullable', 'in:asc,desc'],
            'search' => ['nullable'],
        ];
    }

    public function field(): string
    {
        return $this->validated('field') ?? 'name';
    }
}
