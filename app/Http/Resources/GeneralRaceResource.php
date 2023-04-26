<?php

namespace App\Http\Resources;

use App\Models\Race;
use App\Support\Http\CustomJsonResource;

/** @mixin Race */
class GeneralRaceResource extends CustomJsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'season' => $this->season_id,
            'season_name' => $this->season->full_name,
            'name' => $this->name,
            'qualifying_started' => $this->qualifying_started,
            'qualifying_completed' => $this->qualifying_completed,
            'started' => $this->started,
            'completed' => $this->completed,
        ];
    }
}
