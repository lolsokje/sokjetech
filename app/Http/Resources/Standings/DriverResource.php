<?php

namespace App\Http\Resources\Standings;

use App\Models\DriverChampionshipStanding;
use App\Support\Http\CustomJsonResource;

/** @mixin DriverChampionshipStanding */
class DriverResource extends CustomJsonResource
{
    public function toArray($request): array
    {
        return [
            'position' => $this->position,
            'points' => $this->points,
            'name' => $this->driver->full_name,
            'entrants' => [],
        ];
    }
}
