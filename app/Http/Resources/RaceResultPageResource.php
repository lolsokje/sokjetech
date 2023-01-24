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
            'full_name' => $driver->full_name,
            'number' => $racer->number,
            'team' => [
                'background_colour' => $entrant->accent_colour,
                'style_string' => $entrant->style_string,
                'team_name' => $entrant->full_name,
            ],
            'result' => [
                'starting_position' => $this->starting_position,
                'position' => $this->position,
                'dnf' => $this->dnf,
                'fastest_lap' => $this->fastest_lap,
                'points' => $this->points,
            ],
        ];
    }
}
