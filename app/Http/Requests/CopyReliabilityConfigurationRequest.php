<?php

namespace App\Http\Requests;

use Gate;

class CopyReliabilityConfigurationRequest extends SeasonCopyRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('season')->universe);
    }

    public function rules(): array
    {
        return [
            'season_id' => ['required', 'exists:seasons,id'],
        ];
    }
}
