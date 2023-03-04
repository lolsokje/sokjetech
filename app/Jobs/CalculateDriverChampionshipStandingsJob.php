<?php

namespace App\Jobs;

use App\Actions\CalculateDriverTieBreaker;
use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateDriverChampionshipStandingsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $results = [];
    private Collection $drivers;

    public function __construct(private readonly Season $season)
    {
        $this->season->load('raceResults');
        $this->drivers = $this->season->drivers->load('raceResults')->keyBy('id');
    }

    public function handle(): void
    {
        if (! count($this->season->raceResults)) {
            return;
        }

        $this->season->driverChampionshipStandings()->delete();

        $this->cacheResults();
        $this->sortResults();
        $this->addPositionToResults();
        $this->storeStandings();
    }

    private function cacheResults(): void
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

    private function sortResults(): void
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

    private function addPositionToResults(): void
    {
        $position = 1;

        foreach ($this->results as $key => $result) {
            $this->results[$key]['position'] = $position;
            $position++;
        }
    }

    private function storeStandings(): void
    {
        foreach ($this->results as $result) {
            $this->season->driverChampionshipStandings()->create($result);
        }
    }
}
