<?php

namespace App\Http\Resources;

use App\Http\Resources\RaceWeekend\QualifyingResultResource;
use App\Models\Driver;
use App\Models\QualifyingResult;
use App\Models\Racer;
use Illuminate\Http\Request;

/** @mixin Driver */
class QualifyingDriverResource extends BaseResultResource
{
    public function toArray(Request $request): array
    {
        /** @var Racer $racer */
        $racer = $this->resource['racer'];
        /** @var QualifyingResult $result */
        $result = $this->resource['result'];

        return [
            'id' => $racer->id,
            'entrant_id' => $racer->entrant_id,
            'full_name' => $racer->driver->full_name,
            'number' => $racer->number,
            'ratings' => $this->getRatings($racer, $result),
            'team' => $this->getTeamDetails($racer),
            'result' => $result ? QualifyingResultResource::array($result) : QualifyingResultResource::default(),
        ];
    }
}
