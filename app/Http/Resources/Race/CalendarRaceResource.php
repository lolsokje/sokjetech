<?php

namespace App\Http\Resources\Race;

use App\Http\Resources\CircuitResource;
use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
class CalendarRaceResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order' => $this->order,
            'circuit' => (new CircuitResource($this->circuit))->toArray($request),
            'name' => $this->name,
            'qualifying_started' => $this->qualifying_started,
            'qualifying_completed' => $this->qualifying_completed,
            'started' => $this->started,
            'completed' => $this->completed,
            'duration' => $this->raceDuration()->readable(),
            'postfix' => $this->raceDuration()->postfix(),
        ];
    }
}
