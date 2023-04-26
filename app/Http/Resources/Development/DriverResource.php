<?php

namespace App\Http\Resources\Development;

use App\Models\Racer;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Racer */
class DriverResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->driver->full_name,
            'age' => $this->age(),
            'number' => $this->number,
            'team_name' => $this->entrant->full_name,
            'team_style' => $this->entrant->style_string,
            'accent_colour' => $this->entrant->accent_colour,
            'rating' => $this->rating,
            'reliability' => $this->reliability,
            'min' => 0,
            'max' => 0,
        ];
    }
}
