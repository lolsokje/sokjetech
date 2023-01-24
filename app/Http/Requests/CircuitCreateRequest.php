<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CircuitCreateRequest extends FormRequest
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
            'name' => ['required'],
            'country' => ['required'],
            'shared' => ['nullable', 'boolean'],
        ];
    }

    public function data(): array
    {
        return array_merge(
            $this->validated(),
            ['shared' => $this->request->has('shared')],
        );
    }
}
