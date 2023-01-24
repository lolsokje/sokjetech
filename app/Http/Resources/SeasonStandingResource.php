<?php

namespace App\Http\Resources;

use App\Models\Season;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Season */
class SeasonStandingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'last_point_paying_position' => $this->lastPointPayingPosition(),
        ];
    }
}
