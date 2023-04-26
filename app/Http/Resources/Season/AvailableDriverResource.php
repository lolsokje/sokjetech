<?php

namespace App\Http\Resources\Season;

use App\Models\Driver;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin Driver */
class AvailableDriverResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
        ];
    }
}
