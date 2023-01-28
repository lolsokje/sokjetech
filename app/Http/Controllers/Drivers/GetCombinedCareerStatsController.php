<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Driver;

class GetCombinedCareerStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        $driver->load([
            'results' => [
                'race' => ['circuit'],
                'season' => ['series'],
            ],
        ]);

        $results = [];

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

            $results[$series->name][$result->season->year][$race->order] = $data;
        }

        return $results;
    }
}
