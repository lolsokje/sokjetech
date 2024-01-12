<?php

namespace App\Http\Resources\Race\Results;

use App\ValueObjects\Race\Results\DriverPerformance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DriverPerformance */
class DriverPerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'starting_position' => $this->startingPosition,
            'position' => $this->position,
            'position_change' => $this->positionChange,
            'stints' => $this->stints,
            'stints_total' => $this->stintsTotal,
            'race_total' => $this->raceTotal,
            'fastest_lap_roll' => $this->fastestLapRoll,
            'dnf' => $this->dnf,
            'fastest_lap' => $this->fastestLap,
            'points' => $this->points,
        ];
    }
}
