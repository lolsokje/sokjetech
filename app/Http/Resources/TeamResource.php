<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Team */
class TeamResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'short_name' => $this->short_name,
            'team_principal' => $this->team_principal,
            'country' => $this->country,
            'primary_colour' => $this->primary_colour,
            'secondary_colour' => $this->secondary_colour,
            'accent_colour' => $this->accent_colour,
            'style_string' => $this->style_string,
        ];
    }
}
