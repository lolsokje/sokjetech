<?php

namespace App\Http\Requests;

use App\Enums\BugStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BugUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(BugStatus::class)],
            'admin_remarks' => ['nullable'],
        ];
    }
}
