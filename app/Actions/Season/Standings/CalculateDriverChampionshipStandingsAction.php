<?php

namespace App\Actions\Season\Standings;

use App\Contracts\CalculateChampionshipStandingsContract;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateDriverChampionshipStandingsAction extends CalculateChampionshipStandingsAction implements
    CalculateChampionshipStandingsContract
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Season $season)
    {
        parent::__construct($season, 'racer_id');

        $this->season->load('driversWithParticipation.raceResults', 'raceResults');
        $this->models = $this->season->driversWithParticipation->keyBy('id');
    }

    public function clearExistingStandings(): void
    {
        $this->season->driverChampionshipStandings()->delete();
    }

    public function cacheResults(): void
    {
        foreach ($this->season->raceResults as $result) {
            $racerId = $result->racer_id;
            if (! array_key_exists($racerId, $this->results)) {
                $this->results[$racerId] = [
                    'season_id' => $this->season->id,
                    'racer_id' => $racerId,
                    'points' => 0,
                ];
            }

            $this->results[$racerId]['points'] += $result->points;
        }
    }

    public function storeStandings(): void
    {
        foreach ($this->results as $result) {
            $this->season->driverChampionshipStandings()->create($result);
        }
    }
}
