<?php

namespace App\Actions\Season\Standings;

abstract class CalculateChampionshipStandingsAction
{
    public function hasResults(): bool
    {
        return count($this->season->raceResults) > 0;
    }
}
