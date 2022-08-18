<?php

namespace App\Actions\Races\Configuration;

use App\Http\Requests\StoreReliabilityConfigurationRequest;
use App\Models\ReliabilityReason;
use App\Models\Season;

class StoreReliabilityConfiguration
{
    public function __construct(protected StoreReliabilityConfigurationRequest $request, protected Season $season)
    {
    }

    public function handle(): void
    {
        $this->updateReliabilityRngValues();
        $this->clearExistingReasons();
        $this->storeReliabilityReasons();
    }

    private function updateReliabilityRngValues(): void
    {
        $this->season->reliabilityConfiguration()->updateOrCreate(
            ['season_id' => $this->season->id],
            ['min_rng' => $this->request->validated('min_rng'), 'max_rng' => $this->request->validated('max_rng')],
        );
    }

    private function clearExistingReasons(): void
    {
        $this->season->reliabilityReasons->each(fn (ReliabilityReason $reason) => $reason->delete());
    }

    private function storeReliabilityReasons(): void
    {
        foreach ($this->request->validated('reasons') as $type => $reasons) {
            $this->storeReasonsForType($type, $reasons);
        }
    }

    private function storeReasonsForType(string $type, array $reasons): void
    {
        foreach ($reasons as $reason) {
            $this->season->reliabilityReasons()->create([
                'type' => $type,
                'reason' => $reason,
            ]);
        }
    }
}
