<?php

namespace App\Http\Requests;

use App\Models\Season;
use Illuminate\Foundation\Http\FormRequest;

class SeasonCopyRequest extends FormRequest
{
    public function getSourceSeason(): Season
    {
        return Season::find($this->request->get('season_id'));
    }
}
