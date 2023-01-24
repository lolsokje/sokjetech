<?php

namespace App\Http\Resources\Development;

use App\Models\EngineSeason;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EngineSeason */
class EngineResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rebadge' => $this->rebadge,
            'individual_rating' => $this->individual_rating,
            'rating' => $this->rating,
            'reliability' => $this->reliability,
            'min' => 0,
            'max' => 0,
            'base_engine_id' => $this->base_engine_id,
        ];
    }
}
