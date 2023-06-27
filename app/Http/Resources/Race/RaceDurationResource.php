<?php

namespace App\Http\Resources\Race;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
class RaceDurationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $duration = $this->raceDuration();
        $editable = $duration->editable();

        return [
            'raw' => $this->duration,
            'readable' => $duration->readable(),
            'postfix' => $duration->postfix(),
            'laps' => $editable,
            'distance' => $editable,
            'hours' => $editable[0] ?? 0,
            'minutes' => $editable[1] ?? 0,
        ];
    }
}
