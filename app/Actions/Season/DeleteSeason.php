<?php

namespace App\Actions\Season;

use App\Models\Season;

class DeleteSeason
{
    public function __construct(
        protected readonly Season $season,
    ) {
    }

    public function handle(): void
    {
        $this->deleteResults();
        $this->deleteRacers();
        $this->deleteChampionshipStandings();
        $this->deleteEntrants();
        $this->deleteEngines();
        $this->deleteRaces();
        $this->deletePointSystem();
        $this->deleteReliabilityConfiguration();
        $this->deleteQualifyingFormat();

        $this->season->delete();
    }

    private function deleteResults(): void
    {
        $this->season->qualifyingResults()->delete();
        $this->season->raceResults()->delete();
    }

    private function deleteRacers(): void
    {
        $this->season->drivers()->delete();
    }

    private function deleteChampionshipStandings(): void
    {
        $this->season->driverChampionshipStandings()->delete();
        $this->season->teamChampionshipStandings()->delete();
    }

    private function deleteEntrants(): void
    {
        $this->season->entrants()->delete();
    }

    private function deleteEngines(): void
    {
        $this->season->engines()->delete();
    }

    private function deleteRaces(): void
    {
        $this->season->races()->delete();
    }

    private function deletePointSystem(): void
    {
        $this->season->pointDistribution()->delete();
        $this->season->pointSystem()->delete();
    }

    private function deleteReliabilityConfiguration(): void
    {
        $this->season->reliabilityReasons()->delete();
        $this->season->reliabilityConfiguration()->delete();
    }

    private function deleteQualifyingFormat(): void
    {
        $format = $this->season->qualifyingFormat;

        if ($format) {
            $format->delete();
        }
    }
}
