<?php

namespace App\Http\Resources\Circuit;

use App\Models\CircuitVariation;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin CircuitVariation */
class CircuitVariationResource extends CustomJsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'length' => new CircuitVariationLengthResource($this),
            'laptime' => new CircuitVariationLaptimeResource($this),
            'multipliers' => new CircuitVariationRatingMultiplierResource($this),
        ];
    }
}
