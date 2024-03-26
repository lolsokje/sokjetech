<?php

namespace App\Http\Resources;

use App\Http\Resources\Race\Results\DriverDetailsResource;
use App\Http\Resources\Race\Results\DriverQualifyingPerformanceResource;
use App\Http\Resources\Race\Results\DriverRatingResource;
use App\Http\Resources\Race\Results\TeamDetailsResource;
use App\Models\QualifyingResult;
use App\ValueObjects\Race\Results\DriverDetails;
use App\ValueObjects\Race\Results\DriverQualifyingPerformance;
use App\ValueObjects\Race\Results\DriverRatings;
use App\ValueObjects\Race\Results\TeamDetails;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin QualifyingResult */
class QualifyingResultResource extends JsonResource
{
    public function toArray($request): array
    {
        parent::withoutWrapping();

        return [
            'id' => (string)$this->racer_id,
            'driver' => DriverDetailsResource::make($this->getDriver()),
            'performance' => DriverQualifyingPerformanceResource::make($this->getPerformance()),
            'ratings' => DriverRatingResource::make($this->getRatings()),
            'team' => TeamDetailsResource::make($this->getTeam()),
        ];
    }

    public function getPerformance(): DriverQualifyingPerformance
    {
        return new DriverQualifyingPerformance(
            position: $this->position,
            runs: $this->runs,
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
            startingBonus: 0,
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
