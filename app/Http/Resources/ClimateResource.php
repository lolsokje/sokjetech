<?php

namespace App\Http\Resources;

use App\Models\Climate;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin Climate */
class ClimateResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'short_name' => $this->short_name,
            'long_name' => $this->long_name,
        ];
    }
}
