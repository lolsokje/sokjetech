<?php

namespace App\Http\Resources;

use App\Models\Entrant;
use Illuminate\Http\Resources\Json\JsonResource;

class RaceWeekendDriverResource extends JsonResource
{
    public function toArray($request): array
    {
        $driver = $this->driver;
        $entrant = $this->entrant;

        [$driverRating, $teamRating, $engineRating, $totalRating] = $this->getRatings($entrant);

        return [
            'id' => $this->id,
            'entrant_id' => $entrant->id,
            'full_name' => $driver->full_name,
            'number' => $this->number,
            'team_name' => $entrant->full_name,
            'short_team_name' => $entrant->short_name,
            'style_string' => $entrant->style_string,
            'driver_rating' => $driverRating,
            'team_rating' => $teamRating,
            'engine_rating' => $engineRating,
            'total_rating' => $totalRating,
            'team_reliability' => $entrant->reliability,
            'engine_reliability' => $entrant->engine->reliability,
            'driver_reliability' => $this->reliability,
            'primary_colour' => $entrant->primary_colour,
            'secondary_colour' => $entrant->secondary_colour,
        ];
    }

    private function getRatings(Entrant $entrant): array
    {
        $driverRating = $this->rating;
        $teamRating = $entrant->rating;
        $engineRating = $entrant->engine->rating;
        $totalRating = $driverRating + $teamRating + $engineRating;

        return [$driverRating, $teamRating, $engineRating, $totalRating];
    }
}
