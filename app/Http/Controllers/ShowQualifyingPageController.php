<?php

namespace App\Http\Controllers;

use App\Http\Resources\QualifyingResultResource;
use App\Http\Resources\RaceWeekendDriverResource;
use App\Models\Race;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ShowQualifyingPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->authorize('view', $race->season->universe);

        $drivers = RaceWeekendDriverResource::collection($this->getRacers($race));
        return Inertia::render('RaceWeekend/Qualifying', [
            'race' => $race,
            'drivers' => $drivers->toArray(request()),
            'qualifyingResults' => QualifyingResultResource::collection($race->qualifyingResults)->toArray(request()),
        ]);
    }

    private function getRacers(Race $race): Collection
    {
        return $race->qualifying_started ? $this->getRacersForRace($race) : $this->getRacersForSeason($race);
    }

    private function getRacersForRace(Race $race): Collection
    {
        $race->load([
            'season' => ['qualifyingFormat'],
            'qualifyingResults' => [
                'racer' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
            ],
        ]);
        return $race->qualifyingResults->map->racer;
    }

    private function getRacersForSeason(Race $race): Collection
    {
        $race->load([
            'season' => [
                'qualifyingFormat',
                'activeRacers' => [
                    'driver',
                    'entrant' => [
                        'engine',
                    ],
                ],
            ],
        ]);

        return $race->season->activeRacers;
    }
}
