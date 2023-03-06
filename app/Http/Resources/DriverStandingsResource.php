<?php

namespace App\Http\Resources;

use App\Models\Racer;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverStandingsResource extends JsonResource
{
    public function toArray($request): array
    {
        $results = $this->getResultsPerRace();

        /** @var Racer $racer */
        $racer = $this->racer;

        return [
            'id' => $racer->id,
            'full_name' => $racer->driver->full_name,
            'position' => $this->position,
            'points' => $this->points,
            'number' => $racer->number,
            'team_name' => $racer->entrant->short_name,
            'background_colour' => $racer->entrant->accent_colour,
            'style_string' => $racer->entrant->style_string,
            'results' => $results,
        ];
    }

    private function getResultsPerRace(): array
    {
        $results = [];

        foreach ($this->racer->raceResults as $result) {
            $results[$result->race->order] = [
                'starting_position' => $result->starting_position,
                'dnf' => $result->dnf,
                'position' => $result->dnf ? 'DNF' : $result->position,
                'fastest_lap' => $result->fastest_lap,
                'points' => $result->points,
            ];
        }

        return $results;
    }
}
