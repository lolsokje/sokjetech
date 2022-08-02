<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class StoreQualifyingResultsRequest extends FormRequest
{
    public function details(): array
    {
        return $this->validated('details');
    }

    public function drivers(): Collection
    {
        return collect($this->validated('drivers'));
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'drivers' => ['required', 'array'],
            'details' => ['required', 'array'],
        ];
    }
}
