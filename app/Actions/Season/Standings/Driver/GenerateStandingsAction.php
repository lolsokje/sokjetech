<?php

namespace App\Actions\Season\Standings\Driver;

use App\Http\Resources\Standings\DriverResource;
use App\Http\Resources\Standings\EntrantResource;
use App\Http\Resources\Standings\RaceResultResource;
use App\Models\Season;

class GenerateStandingsAction
{
    private array $standings = [];

    public function __construct(private readonly Season $season)
    {
    }

    public function handle(): array
    {
        $this->initialiseStandings();
        $this->fillStandings();

        return $this->standings;
    }

    private function initialiseStandings(): void
    {
        foreach ($this->season->driverChampionshipStandings as $standing) {
            $this->standings[$standing->driver_id] = DriverResource::array($standing);
        }

        foreach ($this->season->driversWithParticipation as $racer) {
            $driverId = $racer->driver_id;
            $entrantId = $racer->entrant_id;

            $this->standings[$driverId]['entrants'][$entrantId] = EntrantResource::array($racer);
        }
    }

    private function fillStandings(): void
    {
        foreach ($this->season->raceResults as $result) {
            $driverId = $result->racer->driver_id;
            $entrantId = $result->entrant_id;

            $array = RaceResultResource::array($result);

            $this->standings[$driverId]['entrants'][$entrantId]['results'][$result->race->order] = $array;
        }
    }
}
