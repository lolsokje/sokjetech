<?php

namespace App\Actions;

use App\Http\Requests\RacerCreateRequest;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;

class StoreEntrantRacers
{
    public function __construct(
        protected RacerCreateRequest $request,
        protected Season $season,
        protected Entrant $entrant,
    ) {
    }

    public function handle(): void
    {
        $this->deactivateCurrentRacers();

        $this->addRacersToEntrant();
    }

    private function deactivateCurrentRacers(): void
    {
        $this->entrant->allRacers()->each(fn (Racer $driver) => $driver->update(['active' => false]));
    }

    private function addRacersToEntrant(): void
    {
        foreach ($this->request->drivers() as $driver) {
            $id = $driver['driver_id'];
            [$rating, $reliability] = $this->getLastKnownRatingAndReliability($id);
            $this->entrant->allRacers()->updateOrCreate(
                ['driver_id' => $id],
                [
                    'season_id' => $this->season->id,
                    'number' => $driver['number'],
                    'active' => true,
                    'rating' => $rating,
                    'reliability' => $reliability,
                ],
            );
        }
    }

    private function getLastKnownRatingAndReliability(int $driverId): array
    {
        $racer = Racer::query()
            ->where('driver_id', $driverId)
            ->orderBy('updated_at', 'DESC')->first();

        return [$racer->rating ?? 0, $racer->reliability ?? 0];
    }
}
