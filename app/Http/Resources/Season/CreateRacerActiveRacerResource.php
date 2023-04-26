<?php

namespace App\Http\Resources\Season;

use App\Models\Racer;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin Racer */
class CreateRacerActiveRacerResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'driver_id' => $this->driver_id,
            'full_name' => $this->driver->full_name,
            'number' => $this->number,
            'age' => $this->age(),
        ];
    }
}
