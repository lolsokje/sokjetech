<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CopyEngineRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'series_id' => ['required', 'exists:series,id'],
        ];
    }
}
