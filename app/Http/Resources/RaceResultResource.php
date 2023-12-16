<?php

namespace App\Http\Resources;

use App\Models\Racer;
use App\Models\RaceResult;

class RaceResultResource extends BaseResultResource
{
    public function toArray($request): array
    {
        /** @var Racer $racer */
        $racer = $this->resource['racer'];
        /** @var RaceResult $result */
        $result = $this->resource['result'];

        return [
            'id' => $racer->id,
            'entrant_id' => $racer->entrant_id,
            'first_name' => $racer->driver->first_name,
            'last_name' => $racer->driver->last_name,
            'full_name' => $racer->driver->full_name,
            'number' => $racer->number,
            'team' => $this->getTeamDetails($racer),
            'ratings' => $this->getRatings($racer, $result),
            'result' => $this->getResultDetails($result),
        ];
    }

    private function getResultDetails(?RaceResult $result): array
    {
        return [
            'starting_position' => $result?->starting_position,
            'bonus' => $result?->starting_bonus,
            'position' => $result?->position,
            'dnf' => $result?->dnf,
            'fastest_lap_roll' => $result?->fastest_lap_roll,
            'fastest_lap' => $result?->fastest_lap ?? false,
            'stints' => $result->stints ?? [],
        ];
    }
}
