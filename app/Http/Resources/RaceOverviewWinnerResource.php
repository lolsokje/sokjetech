<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceOverviewWinnerResource extends JsonResource
{
    public function toArray($request): array
    {
        $racer = $this->racer;
        $driver = $racer->driver;
        $entrant = $racer->entrant;

        return [
            'race_id' => (string) $this->race_id,
            'full_name' => $driver->fullName,
            'number' => $racer->number,
            'team_name' => $entrant->short_name,
            'background_colour' => $entrant->accent_colour,
            'style_string' => $entrant->style_string,
        ];
    }
}
