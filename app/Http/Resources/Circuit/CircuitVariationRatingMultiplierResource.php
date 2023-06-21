<?php

namespace App\Http\Resources\Circuit;

use App\Models\CircuitVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CircuitVariation */
class CircuitVariationRatingMultiplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'team' => $this->team_multiplier,
            'engine' => $this->engine_multiplier,
        ];
    }
}
