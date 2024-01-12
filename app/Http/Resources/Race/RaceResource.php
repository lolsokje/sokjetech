<?php

declare(strict_types=1);

namespace App\Http\Resources\Race;

use App\Http\Resources\Circuit\CircuitVariationResource;
use App\Http\Resources\CircuitResource;
use App\Http\Resources\ClimateResource;
use App\Http\Resources\GeneralSeasonResource;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
class RaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        self::withoutWrapping();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
            'qualifying_started' => $this->qualifying_started,
            'qualifying_completed' => $this->qualifying_completed,
            'started' => $this->started,
            'completed' => $this->completed,
            'completed_at' => $this->completed_at,
            'race_details' => $this->race_details,
            'race_type' => $this->race_type,
            'distance_type' => $this->distance_type,
            'duration' => $this->duration,
            'current_lap' => $this->current_lap,
            'climate' => ClimateResource::make($this->whenLoaded('climate')),
            'season' => GeneralSeasonResource::make($this->whenLoaded('season')),
            'circuit' => CircuitResource::make($this->whenLoaded('circuit')),
            'circuit_variation' => CircuitVariationResource::make($this->whenLoaded('variation')),
        ];
    }
}
