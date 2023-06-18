<?php

namespace App\Http\Resources\Circuit;

use App\Models\CircuitVariation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CircuitVariation */
class CircuitVariationLengthResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'default' => $this->length,
            'km' => $this->length_in_kilometers,
            'm' => $this->length_in_miles,
        ];
    }
}
