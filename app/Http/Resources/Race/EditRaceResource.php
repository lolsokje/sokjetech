<?php

namespace App\Http\Resources\Race;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
class EditRaceResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'circuit_id' => $this->circuit_id,
            'circuit_variation_id' => $this->circuit_variation_id,
            'climate_id' => $this->climate_id,
            'duration' => new RaceDurationResource($this),
            'race_type' => $this->race_type,
            'distance_type' => $this->distance_type,
        ];
    }
}
