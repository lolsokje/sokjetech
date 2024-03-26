<?php

namespace App\Actions;

use App\Http\Resources\QualifyingResultResource;
use App\Models\Race;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetQualifyingResults
{
    public function handle(Race $race): AnonymousResourceCollection
    {
        $results = $race->qualifyingResults()->with([
            'racer' => [
                'driver',
                'entrant' => ['engine'],
            ],
        ])->get();

        return QualifyingResultResource::collection($results);
    }
}
