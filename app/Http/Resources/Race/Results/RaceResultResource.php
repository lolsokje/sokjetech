<?php

declare(strict_types=1);

namespace App\Http\Resources\Race\Results;

use App\Models\RaceResult;
use App\ValueObjects\Race\Results\DriverDetails;
use App\ValueObjects\Race\Results\DriverPerformance;
use App\ValueObjects\Race\Results\DriverRatings;
use App\ValueObjects\Race\Results\TeamDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RaceResult */
class RaceResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        parent::withoutWrapping();

        return [
            'id' => $this->id,
            'driver' => DriverDetailsResource::make($this->getDriver()),
            'ratings' => DriverRatingResource::make($this->getRatings()),
            'performance' => DriverPerformanceResource::make($this->getPerformance()),
            'team' => TeamDetailsResource::make($this->getTeam()),
        ];
    }

    private function getPerformance(): DriverPerformance
    {
        return new DriverPerformance(
            startingPosition: $this->starting_position,
            position: $this->position,
            positionChange: $this->starting_position - $this->position,
            stints: $this->stints ?? [],
            raceTotal: $this->total,
            fastestLapRoll: $this->fastest_lap_roll,
            dnf: $this->dnf,
            fastestLap: $this->fastest_lap,
            points: $this->points,
        );
    }

    private function getDriver(): DriverDetails
    {
        return new DriverDetails(
            firstName: $this->racer->driver->first_name,
            lastName: $this->racer->driver->last_name,
            number: $this->racer->number,
        );
    }

    private function getRatings(): DriverRatings
    {
        return new DriverRatings(
            driverRating: $this->driver_rating,
            driverReliability: $this->racer->reliability,
            teamRating: $this->team_rating,
            teamReliability: $this->racer->entrant->reliability,
            engineRating: $this->engine_rating,
            engineReliability: $this->racer->entrant->engine?->reliability,
            startingBonus: $this->starting_bonus,
        );
    }

    private function getTeam(): TeamDetails
    {
        return new TeamDetails(
            name: $this->racer->entrant->full_name,
            styleString: $this->racer->entrant->style_string,
            accentColour: $this->racer->entrant->accent_colour,
        );
    }
}
