<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StartingGridResource extends JsonResource
{
    public function toArray($request): array
    {
        $racer = $this->racer;
        $driver = $racer->driver;
        $entrant = $racer->entrant;

        return [
            'id' => $racer->id,
            'full_name' => $driver->full_name,
            'number' => $racer->number,
            'team' => [
                'team_name' => $entrant->short_name,
                'style_string' => $entrant->style_string,
                'background_colour' => $entrant->accent_colour,
            ],
            'result' => [
                'position' => $this->position,
            ],
        ];
    }
}
