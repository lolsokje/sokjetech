<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BugStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required'],
            'summary' => ['required', 'max:255'],
            'details' => ['required'],
        ];
    }
}
