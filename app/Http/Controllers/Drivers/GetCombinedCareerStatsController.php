<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
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

        // TODO per-entrant results
        // driver_id 50662012769931264
        // universe_id 50653958636703744
        foreach ($driver->results as $result) {
            $series = $result->season->series;
            $race = $result->race;

            $data = [
                'id' => $result->id,
                'race_id' => $race->id,
                'name' => $race->name,
                'round' => $race->order,
                'year' => $result->season->year,
                'circuit_country' => $race->circuit->country,
                'starting_position' => $result->starting_position,
                'position' => $result->position,
                'fastest_lap' => $result->fastest_lap,
                'dnf' => $result->dnf,
                'points' => $result->points,
            ];

            $results[$series->name][$result->season->year]['races'][$race->order] = $data;
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
