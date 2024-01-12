<?php

use App\Actions\GetRaceResults;
use App\Http\Resources\Race\RaceResource;
use App\Models\Race;
use App\Models\Season;

it('redirects to qualifying when the qualifying has not been completed', function () {
    $season = Season::factory()->started()->create();
    $user = $season->universe->user;
    $race = Race::factory()->for($season)->create();

    $this->actingAs($user)
        ->get(route('weekend.race', $race))
        ->assertRedirectToRoute('weekend.qualifying', $race);
});

it('shows the race page when qualifying has been completed', function () {
    $season = Season::factory()->started()->create();
    $user = $season->series->universe->user;
    $race = Race::factory()->for($season)->raceReady()->create();

    $this->actingAs($user)
        ->get(route('weekend.race', $race))
        ->assertOk()
        ->assertComponent('RaceWeekend/Race')
        ->assertHasResource('race', RaceResource::make($race->load('season', 'circuit')))
        ->assertHasResource('results', (new GetRaceResults)->handle($race));
});
