<?php

namespace App\Http\Resources\Standings;

use App\Models\Racer;
use App\Support\Http\CustomJsonResource;

/** @mixin Racer */
class EntrantResource extends CustomJsonResource
{
    public function toArray($request): array
    {
        return [
            'number' => $this->number,
            'name' => $this->entrant->short_name,
            'style_string' => $this->entrant->style_string,
            'accent_colour' => $this->entrant->accent_colour,
            'results' => [],
        ];
    }
}
