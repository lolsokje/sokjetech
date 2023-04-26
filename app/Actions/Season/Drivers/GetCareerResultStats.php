<?php

namespace App\Actions\Season\Drivers;

use App\Http\Resources\Standings\RaceResultResource;
use App\Models\Driver;
use App\Models\DriverChampionshipStanding;

class GetCareerResultStats
{
    private array $results = [];

    public function __construct(
        protected readonly Driver $driver,
    ) {
        $this->driver->load([
            'results' => [
                'race' => ['circuit'],
                'season' => ['series'],
            ],
            'championshipResults.season.series',
        ]);
    }

    public function handle(): array
    {
        foreach ($this->driver->results as $result) {
            $series = $result->season->series;
            $race = $result->race;

            $resource = RaceResultResource::array($result);
            $this->results[$series->name][$result->season->year]['races'][$race->order] = $resource;
        }

        $this->driver->championshipResults->each(function (DriverChampionshipStanding $result) {
            $season = $result->season;
            $series = $season->series;

            $this->results[$series->name][$season->year]['position'] = $result->position;
            $this->results[$series->name][$season->year]['points'] = $result->points;
        });

        return $this->results;
    }
}
