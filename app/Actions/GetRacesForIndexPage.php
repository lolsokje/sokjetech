<?php

namespace App\Actions;

use App\Models\Race;
use Illuminate\Support\Collection;

class GetRacesForIndexPage
{
    public function handle(): Collection
    {
        return Race::query()
            ->with('season.series.universe.user')
            ->latestCompleted()
            ->visible()
            ->get();
    }
}
