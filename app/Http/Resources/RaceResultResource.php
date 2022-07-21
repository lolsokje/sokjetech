<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceResultResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driver_id' => (string) $this->racer_id,
            'starting_position' => $this->starting_position,
            'position' => $this->position,
            'driver_rating' => $this->driver_rating,
            'team_rating' => $this->team_rating,
            'engine_rating' => $this->engine_rating,
            'starting_bonus' => $this->starting_bonus,
            'stints' => $this->stints ?? [],
            'dnf' => $this->dnf,
        ];
    }
}
