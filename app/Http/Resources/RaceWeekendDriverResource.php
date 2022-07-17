<?php

namespace App\Http\Resources;

use App\Models\Entrant;
use Illuminate\Http\Resources\Json\JsonResource;

class RaceWeekendDriverResource extends JsonResource
{
    public function toArray($request): array
    {
        $racer = $this;
        $driver = $racer->driver;
        $entrant = $racer->entrant;

        [$driverRating, $teamRating, $engineRating, $totalRating] = $this->getRatings($entrant);

        return [
            'id' => $racer->id,
            'full_name' => $driver->full_name,
            'number' => $racer->number,
            'team_name' => $entrant->full_name,
            'style_string' => $entrant->style_string,
            'driver_rating' => $driverRating,
            'team_rating' => $teamRating,
            'engine_rating' => $engineRating,
            'total_rating' => $totalRating,
            'team_reliability' => $entrant->reliability,
            'engine_reliability' => $entrant->engine->rating,
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
