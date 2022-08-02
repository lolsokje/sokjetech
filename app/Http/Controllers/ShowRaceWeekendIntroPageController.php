<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverStandingsResource;
use App\Http\Resources\TeamStandingsResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceWeekendIntroPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->eagerLoadRelationships($race);

        $driverStandings = DriverStandingsResource::collection($race->season->drivers)->toArray(request());
        $teamStandings = TeamStandingsResource::collection($race->season->entrants)->toArray(request());

        return Inertia::render('RaceWeekend/Intro', [
            'race' => $race,
            'teamStandings' => $this->getTeamResultsUntilSelectedRace($race, $teamStandings),
            'driverStandings' => $this->getDriverResultsUntilSelectedRace($race, $driverStandings),
        ]);
    }

    private function eagerLoadRelationships(Race $race): void
    {
        $race->load([
            'stints',
            'season' => [
                'drivers' => [
                    'driver',
                    'entrant',
                    'raceResults' => [
                        'race',
                    ],
                ],
                'entrants' => [
                    'allRacers',
                    'raceResults' => [
                        'racer',
                        'race',
                    ],
                ],
            ],
        ]);
    }

    private function getTeamResultsUntilSelectedRace(Race $race, array $standings): array
    {
        foreach ($standings as $key => $team) {
            $standings[$key]['results'] = $this->getDriverResultsUntilSelectedRace($race, $team['results']);
        }

        return $standings;
    }

    private function getDriverResultsUntilSelectedRace(Race $race, array $standings): array
    {
        $currentRaceOrder = $race->order;

        foreach ($standings as $key => $standing) {
            foreach ($standing['results'] as $raceOrder => $result) {
                if ($raceOrder >= $currentRaceOrder) {
                    unset($standings[$key]['results'][$raceOrder]);
                }
            }
        }
        return $standings;
    }
}
