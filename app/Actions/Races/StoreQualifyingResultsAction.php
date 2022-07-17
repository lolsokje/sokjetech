<?php

namespace App\Actions\Races;

use App\Models\QualifyingResult;
use App\Models\Race;
use Illuminate\Support\Collection;

class StoreQualifyingResultsAction
{
    public function __construct(protected array $details, protected Collection $drivers, protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->race->update(['details' => $this->details]);

        foreach ($this->drivers as $driver) {
            $this->updateOrCreateQualifyingResult($driver);
        }
    }

    private function updateOrCreateQualifyingResult(array $driver): void
    {
        QualifyingResult::updateOrCreate(
            [
                'race_id' => $this->race->id,
                'racer_id' => $driver['id'],
            ],
            [
                'position' => $driver['position'],
                'runs' => $driver['runs'],
                'season_id' => $this->race->season_id,
            ],
        );
    }
}
