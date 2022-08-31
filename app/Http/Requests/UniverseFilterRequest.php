<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniverseFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:name,user'],
            'direction' => ['nullable', 'in:asc,desc'],
            'mine' => ['nullable'],
        ];
    }

    public function onlyMine(): bool
    {
        return $this->validated('mine') === 'true';
    }
}
