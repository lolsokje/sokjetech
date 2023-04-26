<?php

namespace App\Http\Resources\Season;

use App\Models\Entrant;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin Entrant */
class CreateRacerEntrantResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'active_racers' => CreateRacerActiveRacerResource::collectionArray($this->whenLoaded('activeRacers')),
        ];
    }
}
