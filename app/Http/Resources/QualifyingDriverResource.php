<?php

namespace App\Http\Resources;

use App\Models\Driver;
use App\Models\QualifyingResult;
use App\Models\Racer;
use Illuminate\Http\Request;

/** @mixin Driver */
class QualifyingDriverResource extends BaseResultResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
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
            'result' => [
                'runs' => $result ? $result->runs : [],
                'position' => $result?->position,
            ],
        ];
    }
}
