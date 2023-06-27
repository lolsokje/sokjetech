<?php

namespace App\Http\Resources\Season;

use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Season */
class CalendarSeasonResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'series' => [
                'id' => $this->series_id,
                'name' => $this->series->name,
            ],
            'started' => $this->started,
            'completed' => $this->completed,
            'can_start' => $this->can_start,
            'can_complete' => $this->can_complete,
        ];
    }
}
