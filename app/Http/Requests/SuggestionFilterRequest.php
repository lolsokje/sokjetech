<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuggestionFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'field' => ['nullable', 'in:summary,type,status'],
            'direction' => ['nullable', 'in:asc,desc'],
            'page' => ['nullable', 'integer'],
            'only' => ['nullable', 'in:closed,open'],
        ];
    }

    public function field(): string
    {
        return $this->validated('field') ?? 'created_at';
    }

    public function direction(): string
    {
        return $this->validated('direction') ?? 'asc';
    }

    public function page(): ?int
    {
        return $this->validated('page');
    }
}
