<?php

namespace App\Http\Resources;

use App\Models\QualifyingResult;
use App\Models\Racer;
use App\Models\RaceResult;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResultResource extends JsonResource
{
    public static function create(Racer $racer, RaceResult|QualifyingResult|null $result): BaseResultResource
    {
        return self::make([
            'racer' => $racer,
            'result' => $result,
        ]);
    }

    protected function getRatings(Racer $racer, RaceResult|QualifyingResult|null $result): array
    {
        $driverRating = $result ? $result->driver_rating : $racer->rating;
        $teamRating = $result ? $result->team_rating : $racer->entrant->rating;
        $engineRating = $result ? $result->engine_rating : $racer->entrant->engine?->rating;

        return [
            'driver_rating' => $driverRating,
            'team_rating' => $teamRating,
            'engine_rating' => $engineRating,
            'total_rating' => $driverRating + $teamRating + $engineRating,
            'driver_reliability' => $racer->reliability,
            'team_reliability' => $racer->entrant->reliability,
            'engine_reliability' => $racer->entrant->engine?->reliability,
        ];
    }

    protected function getTeamDetails(Racer $racer): array
    {
        return [
            'team_name' => $racer->entrant->full_name,
            'short_team_name' => $racer->entrant->short_name,
            'style_string' => $racer->entrant->style_string,
            'primary_colour' => $racer->entrant->primary_colour,
            'secondary_colour' => $racer->entrant->secondary_colour,
            'accent_colour' => $racer->entrant->accent_colour,
        ];
    }
}
