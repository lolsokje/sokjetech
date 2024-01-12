<?php

namespace App\Http\Resources\Race\Results;

use App\ValueObjects\Race\Results\TeamDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TeamDetails */
class TeamDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'style_string' => $this->styleString,
            'accent_colour' => $this->accentColour,
        ];
    }
}
