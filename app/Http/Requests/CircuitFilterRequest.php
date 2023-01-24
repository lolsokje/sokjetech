<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CircuitFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable'],
            'field' => ['nullable', 'in:name,country'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function search(): ?string
    {
        return $this->validated('search');
    }

    public function field(): string
    {
        return $this->validated('field') ?? 'name';
    }

    public function direction(): string
    {
        return $this->validated('direction') ?? 'asc';
    }
}
