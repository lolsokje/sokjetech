<?php

namespace App\Http\Resources;

use App\Models\Circuit;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Circuit */
class CircuitResource extends JsonResource
{
    public function toArray($request): array
    {
        $climate = $this->relationLoaded('defaultClimate') ? ClimateResource::array($this->defaultClimate) : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'default_climate' => $climate,
            'shared' => $this->shared,
            'races_count' => $this->races_count ?? null,
        ];
    }
}
