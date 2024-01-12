<?php

namespace App\Http\Resources\Race\Results;

use App\ValueObjects\Race\Results\DriverRatings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DriverRatings */
class DriverRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'starting_bonus' => $this->startingBonus,
            'driver_rating' => $this->driverRating,
            'team_rating' => $this->teamRating,
            'engine_rating' => $this->engineRating,
            'total' => $this->totalRating,
            'starting_total' => $this->startingTotal,
            'driver_reliability' => $this->driverReliability,
            'team_reliability' => $this->teamReliability,
            'engine_reliability' => $this->engineReliability,
        ];
    }
}
