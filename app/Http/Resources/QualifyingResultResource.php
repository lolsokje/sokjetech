<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QualifyingResultResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driver_id' => (string) $this->racer_id,
            'driver_rating' => $this->driver_rating,
            'team_rating' => $this->team_rating,
            'engine_rating' => $this->engine_rating,
            'position' => $this->position,
            'runs' => $this->runs ?? [],
        ];
    }
}
