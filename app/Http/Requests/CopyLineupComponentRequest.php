<?php

namespace App\Http\Requests;

use Gate;

class CopyLineupComponentRequest extends SeasonCopyRequest
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

    public function copyRatings(): bool
    {
        return (bool) $this->validated('copy_ratings');
    }
}
