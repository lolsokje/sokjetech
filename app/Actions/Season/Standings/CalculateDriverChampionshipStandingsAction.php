<?php

namespace App\Actions\Season\Standings;

use App\Contracts\CalculateChampionshipStandingsContract;
use App\Models\Racer;
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
        parent::__construct($season, 'driver_id');

        $this->season->load('driversWithParticipation.raceResults', 'raceResults.racer');
        $this->models = $this->season->driversWithParticipation->mapWithKeys(fn (
            Racer $racer,
        ) => [$racer->driver_id => $racer]);
    }

    public function clearExistingStandings(): void
    {
        $this->season->driverChampionshipStandings()->delete();
    }

    public function cacheResults(): void
    {
        foreach ($this->season->raceResults as $result) {
            $driverId = $result->racer->driver_id;
            if (! array_key_exists($driverId, $this->results)) {
                $this->results[$driverId] = [
                    'season_id' => $this->season->id,
                    'driver_id' => $driverId,
                    'points' => 0,
                ];
            }

            $this->results[$driverId]['points'] += $result->points;
        }
    }

    public function storeStandings(): void
    {
        foreach ($this->results as $result) {
            $this->season->driverChampionshipStandings()->create($result);
        }
    }
}
