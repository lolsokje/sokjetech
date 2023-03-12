<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Standings\RaceResultResource;
use App\Models\Driver;
use App\Models\DriverChampionshipStanding;

class GetCombinedCareerStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        $driver->load([
            'results' => [
                'race' => ['circuit'],
                'season' => ['series'],
            ],
            'championshipResults.season.series',
        ]);

        $results = [];

        foreach ($driver->results as $result) {
            $series = $result->season->series;
            $race = $result->race;

            $results[$series->name][$result->season->year]['races'][$race->order] = RaceResultResource::array($result);
        }

        $driver->championshipResults->each(function (DriverChampionshipStanding $result) use (&$results) {
            $season = $result->season;
            $series = $season->series;

            $results[$series->name][$season->year]['position'] = $result->position;
            $results[$series->name][$season->year]['points'] = $result->points;
        });

        return $results;
    }
}
