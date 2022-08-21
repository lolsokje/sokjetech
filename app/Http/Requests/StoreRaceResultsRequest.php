<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class StoreRaceResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function details(): array
    {
        return $this->validated('race_details');
    }

    public function drivers(): Collection
    {
        return collect($this->validated('drivers'));
    }

    public function rules(): array
    {
        return [
            'drivers' => ['required', 'array'],
            'race_details' => ['required', 'array'],
        ];
    }
}
