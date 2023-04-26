<?php

namespace App\Actions\Season\Standings;

use App\Contracts\CalculateChampionshipStandingsContract;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateTeamChampionshipStandingsAction extends CalculateChampionshipStandingsAction implements
    CalculateChampionshipStandingsContract
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Season $season)
    {
        parent::__construct($season, 'entrant_id');

        $this->season->load('entrants.raceResults', 'raceResults');
        $this->models = $this->season->entrants->keyBy('id');
    }

    public function clearExistingStandings(): void
    {
        $this->season->teamChampionshipStandings()->delete();
    }

    public function cacheResults(): void
    {
        foreach ($this->season->raceResults as $result) {
            $entrantId = $result->entrant_id;

            if (! array_key_exists($entrantId, $this->results)) {
                $this->results[$entrantId] = [
                    'season_id' => $this->season->id,
                    'entrant_id' => $entrantId,
                    'points' => 0,
                ];
            }

            $this->results[$entrantId]['points'] += $result->points;
        }
    }

    public function storeStandings(): void
    {
        foreach ($this->results as $result) {
            $this->season->teamChampionshipStandings()->create($result);
        }
    }
}
