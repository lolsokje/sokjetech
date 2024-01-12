<?php

namespace App\Actions;

use App\Http\Resources\Race\Results\RaceResultResource;
use App\Models\Race;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetRaceResults
{
    public function handle(Race $race): AnonymousResourceCollection
    {
        $results = $race->raceResults()->with([
            'racer' => [
                'driver',
                'entrant' => ['engine'],
            ],
        ])->get();

        return RaceResultResource::collection($results);
    }
}
