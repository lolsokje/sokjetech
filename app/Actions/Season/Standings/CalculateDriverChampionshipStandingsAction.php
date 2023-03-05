<?php

namespace App\Actions\Season\Standings;

use App\Actions\CalculateDriverTieBreaker;
use App\Contracts\CalculateDriverChampionshipContract;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateDriverChampionshipStandingsAction extends CalculateChampionshipStandingsAction implements
    CalculateDriverChampionshipContract
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $results = [];
    private Collection $drivers;

    public function __construct(protected readonly Season $season)
    {
        $this->season->load('driversWithParticipation.raceResults', 'raceResults');
        $this->drivers = $this->season->driversWithParticipation->keyBy('id');
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

    public function sortResults(): void
    {
        $action = new CalculateDriverTieBreaker($this->season);

        uasort($this->results, function ($driverOne, $driverTwo) use ($action) {
            if ($driverOne['points'] === $driverTwo['points']) {
                return $action->handle(
                    $this->drivers[$driverTwo['racer_id']],
                    $this->drivers[$driverOne['racer_id']],
                );
            }

            return $driverOne['points'] < $driverTwo['points'];
        });
    }

    public function addPositionToResults(): void
    {
        $position = 1;

        foreach ($this->results as $key => $result) {
            $this->results[$key]['position'] = $position;
            $position++;
        }
    }

    public function storeStandings(): void
    {
        foreach ($this->results as $result) {
            $this->season->driverChampionshipStandings()->create($result);
        }
    }
}
