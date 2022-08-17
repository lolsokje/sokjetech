<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CopyQualifyingFormatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', request()->route('season')->universe);
    }

    public function rules(): array
    {
        return [
            'season_id' => ['required', 'exists:seasons,id'],
        ];
    }
}
