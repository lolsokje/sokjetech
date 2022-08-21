<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'full_name' => $this->resource->full_name,
            'short_name' => $this->resource->short_name,
            'team_principal' => $this->resource->team_principal,
            'country' => $this->resource->country,
            'primary_colour' => $this->resource->primary_colour,
        ];
    }
}
