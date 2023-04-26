<?php

namespace App\Actions\Races\Configuration;

use App\DataTransferObjects\Configuration\PointSystemConfiguration;
use App\Models\PointDistribution;
use App\Models\Season;

class StorePointsSystemConfiguration
{
    public function __construct(
        protected PointSystemConfiguration $configuration,
        protected Season $season,
    ) {
    }

    public function handle(): void
    {
        $this->season->pointDistribution()->each(function (PointDistribution $pointDistribution) {
            $pointDistribution->delete();
        });
        $this->season->pointSystem()->delete();

        $pointSystem = $this->season->pointSystem()->create($this->configuration->toArray());

        (new StorePointDistributions($pointSystem, $this->configuration->points))->handle();
    }
}
