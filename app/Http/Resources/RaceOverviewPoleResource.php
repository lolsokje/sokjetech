<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceOverviewPoleResource extends JsonResource
{
    public function toArray($request): array
    {
        $racer = $this->racer;
        $driver = $racer->driver;
        return [
            'race_id' => (string) $this->race_id,
            'full_name' => $driver->fullName,
        ];
    }
}
