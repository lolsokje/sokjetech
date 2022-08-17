<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class CopyTeamSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', request()->route('season')->universe);
    }

    public function rules(): array
    {
        return [
            'season_id' => ['required', 'exists:seasons,id'],
            'copy_ratings' => ['boolean', 'nullable'],
        ];
    }
}
