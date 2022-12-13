<?php

namespace App\Http\Resources\Development;

use App\Models\Entrant;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Entrant */
class TeamResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'style' => $this->style_string,
            'accent_colour' => $this->accent_colour,
            'rating' => $this->rating,
            'reliability' => $this->reliability,
            'min' => 0,
            'max' => 0,
        ];
    }
}
