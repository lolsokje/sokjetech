<?php

use App\Http\Resources\StartingGridResource;
use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Season;

it('shows the correct starting grid', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->raceReady()->create();
    QualifyingResult::factory(5)->for($race)->sequence(
        ['position' => 1],
        ['position' => 2],
        ['position' => 3],
        ['position' => 4],
        ['position' => 5],
    )->create();

    $race->load([
        'season',
        'qualifyingResults' => [
            'racer' => [
                'driver',
                'entrant',
            ],
        ],
    ]);

    $this->actingAs($season->universe->user)
        ->get(route('weekend.grid', $race))
        ->assertOk()
        ->assertComponent('RaceWeekend/Grid')
        ->assertHasResource('drivers', StartingGridResource::collection($race->qualifyingResults));
});
