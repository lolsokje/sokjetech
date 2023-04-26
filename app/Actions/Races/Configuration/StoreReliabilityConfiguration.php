<?php

namespace App\Actions\Races\Configuration;

use App\DataTransferObjects\Configuration\ReliabilityConfiguration;
use App\Enums\ReliabilityReasonTypes;
use App\Models\ReliabilityReason;
use App\Models\Season;

class StoreReliabilityConfiguration
{
    public function __construct(
        protected readonly ReliabilityConfiguration $configuration,
        protected Season $season,
    ) {
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
            ['min_rng' => $this->configuration->minRng, 'max_rng' => $this->configuration->maxRng],
        );
    }

    private function clearExistingReasons(): void
    {
        $this->season->reliabilityReasons->each(fn (ReliabilityReason $reason) => $reason->delete());
    }

    private function storeReliabilityReasons(): void
    {
        foreach ($this->configuration->reasons as $type => $reasons) {
            $this->storeReasonsForType($type, $reasons);
        }
    }

    private function storeReasonsForType(string $type, array $reasons): void
    {
        foreach ($reasons as $reason) {
            $this->season->reliabilityReasons()->create([
                'type' => ReliabilityReasonTypes::fromString($type),
                'reason' => $reason,
            ]);
        }
    }
}
