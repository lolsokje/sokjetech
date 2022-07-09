<?php

namespace App\Actions;

use App\DataTransferObjects\PointsData;
use App\Models\PointSystem;
use Illuminate\Support\Collection;

class StorePointDistributions implements Action
{
    public function __construct(protected PointSystem $pointSystem, protected Collection $points)
    {
    }

    public function handle(): void
    {
        /** @var PointsData $pointsAwarded */
        foreach ($this->points as $pointsAwarded) {
            $this->pointSystem->pointDistributions()->create([
                'position' => $pointsAwarded->position(),
                'points' => $pointsAwarded->points(),
            ]);
        }
    }
}