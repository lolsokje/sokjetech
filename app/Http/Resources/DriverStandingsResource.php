<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverStandingsResource extends JsonResource
{
    public function toArray($request): array
    {
        $results = $this->getResultsPerRace();

        return [
            'id' => $this->id,
            'full_name' => $this->driver->full_name,
            'number' => $this->number,
            'team_name' => $this->entrant->short_name,
            'background_colour' => $this->entrant->primary_colour,
            'style_string' => $this->entrant->style_string,
            'results' => $results,
        ];
    }

    private function getResultsPerRace(): array
    {
        $results = [];

        foreach ($this->raceResults as $result) {
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
