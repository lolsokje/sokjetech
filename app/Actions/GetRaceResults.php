<?php

namespace App\Actions;

use App\Http\Resources\RaceResultResource;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use Illuminate\Support\Collection;

class GetRaceResults
{
    public function handle(Race $race): array
    {
        $race->load([
            'season' => [
                'qualifyingFormat',
                'drivers' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
            ],
            'raceResults',
        ]);

        $results = $race->raceResults;
        $drivers = $race->season->drivers;

        $drivers = count($results) ?
            $this->getDriversFromResults($results, $drivers) :
            $this->getDriversFromSeason($drivers);

        return array_values(RaceResultResource::collection($drivers)->toArray(request()));
    }

    private function getDriversFromResults(Collection $results, Collection $drivers): Collection
    {
        return $drivers->map(function (Racer $racer) use (&$results) {
            $result = $results->where('racer_id', $racer->id)->first();

            return $result ? $this->buildDriverResult($racer, $result) : null;
        })->filter();
    }

    private function getDriversFromSeason(Collection $drivers): Collection
    {
        $drivers = $drivers->filter(fn (Racer $racer) => $racer->active);

        return $drivers->map(fn (Racer $racer) => $this->buildDriverResult($racer, null));
    }

    private function buildDriverResult(Racer $racer, ?RaceResult $result): RaceResultResource
    {
        return RaceResultResource::create($racer, $result);
    }
}
