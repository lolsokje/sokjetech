<?php

namespace App\Actions\Season\Standings;

use App\Contracts\CalculateDriverChampionshipContract;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateTeamChampionshipStandingsAction extends CalculateChampionshipStandingsAction implements
    CalculateDriverChampionshipContract
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected readonly Season $season)
    {
        $this->season->load('entrants.raceResults', 'raceResults');
    }

    public function clearExistingStandings(): void
    {
    }

    public function cacheResults(): void
    {
    }

    public function sortResults(): void
    {
    }

    public function addPositionToResults(): void
    {
    }

    public function storeStandings(): void
    {
    }
}
