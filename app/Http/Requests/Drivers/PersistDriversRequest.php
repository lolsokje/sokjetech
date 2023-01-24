<?php

namespace App\Http\Requests\Drivers;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class PersistDriversRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('universe'));
    }

    public function rules(): array
    {
        return [
            'drivers' => ['required', 'array'],
        ];
    }
}
