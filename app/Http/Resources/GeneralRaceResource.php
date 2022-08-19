<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralRaceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'season' => $this->season_id,
            'name' => $this->name,
            'qualifying_started' => $this->qualifying_started,
            'qualifying_completed' => $this->qualifying_completed,
            'started' => $this->started,
            'completed' => $this->completed,
        ];
    }
}
