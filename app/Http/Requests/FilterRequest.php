<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function direction(): string
    {
        return $this->validated('direction') ?? 'asc';
    }

    public function search(): ?string
    {
        return $this->validated('search');
    }
}
