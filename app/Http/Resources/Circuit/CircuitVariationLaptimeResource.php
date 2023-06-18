<?php

namespace App\Http\Resources\Circuit;

use App\Models\CircuitVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CircuitVariation */
class CircuitVariationLaptimeResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'base' => $this->base_laptime,
            'readable' => $this->readable_laptime,
        ];
    }
}
