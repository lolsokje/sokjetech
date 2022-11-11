<?php

namespace App\Actions;

use App\Http\Resources\QualifyingDriverResource;
use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Racer;
use Illuminate\Support\Collection;

class GetQualifyingResults
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
            'qualifyingResults',
        ]);

        $results = $race->qualifyingResults;
        $drivers = $race->season->drivers;

        $drivers = count($results) ?
            $this->getDriversFromResults($results, $drivers) :
            $this->getDriversForSeason($drivers);

        return array_values(QualifyingDriverResource::collection($drivers)->toArray(request()));
    }

    private function getDriversFromResults(Collection $results, Collection $drivers): Collection
    {
        return $drivers->map(function (Racer $racer) use (&$results) {
            $result = $results->where('racer_id', $racer->id)->first();

            return $result ? $this->buildDriverResult($racer, $result) : null;
        })->filter();
    }

    private function getDriversForSeason(Collection $drivers): Collection
    {
        $drivers = $drivers->filter(fn (Racer $racer) => $racer->active);

        return $drivers->map(fn (Racer $racer) => $this->buildDriverResult($racer, null));
    }

    private function buildDriverResult(Racer $racer, ?QualifyingResult $result): QualifyingDriverResource
    {
        return QualifyingDriverResource::create($racer, $result);
    }
}
