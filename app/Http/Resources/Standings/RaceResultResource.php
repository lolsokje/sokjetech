<?php

namespace App\Http\Resources\Standings;

use App\Models\RaceResult;
use App\Support\Http\CustomJsonResource;

/** @mixin RaceResult */
class RaceResultResource extends CustomJsonResource
{
    public function toArray($request): array
    {
        return [
            'starting_position' => $this->resource->starting_position,
            'position' => $this->dnf ? 'DNF' : $this->position,
            'dnf' => $this->dnf,
            'fastest_lap' => $this->fastest_lap,
            'points' => $this->points,
        ];
    }
}
