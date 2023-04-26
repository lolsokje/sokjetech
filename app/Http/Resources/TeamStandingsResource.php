<?php

namespace App\Http\Resources;

use App\Models\TeamChampionshipStandings;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TeamChampionshipStandings */
class TeamStandingsResource extends JsonResource
{
    public function toArray($request): array
    {
        $results = $this->getResultsPerRace();
        $entrant = $this->entrant;

        return [
            'id' => $entrant->id,
            'full_name' => $entrant->full_name,
            'points' => $this->points,
            'position' => $this->position,
            'team_name' => $entrant->short_name,
            'team_principal' => $entrant->team_principal,
            'background_colour' => $entrant->accent_colour,
            'style_string' => $entrant->style_string,
            'driver_count' => count($entrant->racersWithParticipation),
            'results' => $results,
        ];
    }

    private function getResultsPerRace(): array
    {
        $results = [];

        foreach ($this->entrant->racersWithParticipation as $racer) {
            $results[$racer->id] = [
                'number' => $racer->number,
                'results' => [],
            ];
        }

        foreach ($this->entrant->raceResults as $result) {
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
