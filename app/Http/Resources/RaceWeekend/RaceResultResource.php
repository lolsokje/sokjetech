<?php

namespace App\Http\Resources\RaceWeekend;

use App\Models\Race;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin Race */
class RaceResultResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'round' => $this->order,
            'season' => [
                'id' => $this->season_id,
                'full_name' => $this->season->full_name,
            ],
            'fastest_lap_point_awarded' => $this->season->pointSystem->fastest_lap_point_awarded,
        ];
    }
}
