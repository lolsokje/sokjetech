<?php

namespace App\Actions\Races;

use App\Models\Driver;
use App\Models\Entrant;
use App\Models\Race;
use App\Models\RaceResult;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalculateIntroPageStandingsAction
{
    private array $driverStandings = [];
    private array $teamStandings = [];

    public function __construct(
        private readonly Race $race,
    ) {
        $this->race->load([
            'season' => [
                'raceResults' => fn (HasMany $query) => $query->with('race', 'racer.driver', 'entrant')
                    ->whereRelation('race', 'order', '<', $race->order),
            ],
        ]);
    }

    public function handle(): array
    {
        foreach ($this->race->season->raceResults as $result) {
            $driver = $result->racer->driver;
            $entrant = $result->entrant;

            $this->initialiseDriverResult($driver);
            $this->initialiseTeamResult($entrant);

            $this->updateDriverResult($result);
            $this->updateTeamResult($result);
        }

        $this->sortStandings();
        $this->getTopThree();

        return [$this->driverStandings, $this->teamStandings];
    }

    private function initialiseDriverResult(Driver $driver): void
    {
        if (! array_key_exists($driver->id, $this->driverStandings)) {
            $this->driverStandings[$driver->id] = [
                'points' => 0,
            ];
        }
    }

    private function initialiseTeamResult(Entrant $entrant): void
    {
        if (! array_key_exists($entrant->id, $this->teamStandings)) {
            $this->teamStandings[$entrant->id] = [
                'points' => 0,
            ];
        }
    }

    private function updateDriverResult(RaceResult $result): void
    {
        $driver = $result->racer->driver;
        $entrant = $result->entrant;

        $this->driverStandings[$driver->id] = [
            'points' => $this->driverStandings[$driver->id]['points'] + $result->points,
            'full_name' => $driver->full_name,
            'number' => $result->racer->number,
            'background_colour' => $entrant->accent_colour,
            'style_string' => $entrant->style_string,
            'team_name' => $entrant->short_name,
        ];
    }

    private function updateTeamResult(RaceResult $result): void
    {
        $entrant = $result->entrant;

        $this->teamStandings[$entrant->id] = [
            'points' => $this->teamStandings[$entrant->id]['points'] + $result->points,
            'full_name' => $entrant->full_name,
            'background_colour' => $entrant->accent_colour,
            'style_string' => $entrant->style_string,
            'team_principal' => $entrant->team_principal,
        ];
    }

    private function sortStandings(): void
    {
        uasort($this->driverStandings, fn ($a, $b) => $a['points'] < $b['points']);
        uasort($this->teamStandings, fn ($a, $b) => $a['points'] < $b['points']);
    }

    private function getTopThree(): void
    {
        $this->driverStandings = array_slice($this->driverStandings, 0, 3);
        $this->teamStandings = array_slice($this->teamStandings, 0, 3);
    }
}
