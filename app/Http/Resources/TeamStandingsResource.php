<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamStandingsResource extends JsonResource
{
    public function toArray($request): array
    {
        $results = $this->getResultsPerRace();

        return [
            'id' => $this->id,
            'team_name' => $this->short_name,
            'background_colour' => $this->primary_colour,
            'style_string' => $this->style_string,
            'driver_count' => count($this->allRacers),
            'results' => $results,
        ];
    }

    private function getResultsPerRace(): array
    {
        $results = [];

        foreach ($this->allRacers as $racer) {
            $results[$racer->id] = [
                'number' => $racer->number,
                'results' => [],
            ];
        }

        foreach ($this->raceResults as $result) {
            $results[$result->racer->id]['results'][$result->race->order] = [
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