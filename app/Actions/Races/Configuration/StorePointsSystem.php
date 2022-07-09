<?php

namespace App\Actions\Races\Configuration;

use App\Http\Requests\PointSystemConfigurationRequest;
use App\Models\PointDistribution;
use App\Models\Season;

class StorePointsSystem
{
    public function __construct(protected PointSystemConfigurationRequest $request, protected Season $season)
    {
    }

    public function handle(): void
    {
        $this->season->pointDistribution()->each(function (PointDistribution $pointDistribution) {
            $pointDistribution->delete();
        });
        $this->season->pointSystem()->delete();

        $validated = $this->request->validated();
        $pointSystem = $this->season->pointSystem()->create($validated);

        (new StorePointDistributions($pointSystem, $this->request->points()))->handle();
    }
}
