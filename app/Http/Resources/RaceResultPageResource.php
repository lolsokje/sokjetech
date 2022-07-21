<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceResultPageResource extends JsonResource
{
    public function toArray($request): array
    {
        $racer = $this->racer;
        $driver = $racer->driver;
        $entrant = $racer->entrant;

        return [
            'position' => $this->position,
            'background_colour' => $entrant->primary_colour,
            'style_string' => $entrant->style_string,
            'full_name' => $driver->full_name,
            'number' => $racer->number,
            'team_name' => $entrant->full_name,
            'dnf' => $this->dnf,
            'points' => $this->points,
        ];
    }
}
