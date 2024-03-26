<?php

use App\Actions\GetRaceResults;
use App\Models\PointSystem;
use App\Models\Race;
use App\Models\RaceResult;
use App\Models\Season;

it('does not show the race result page for unstarted seasons', function () {
    $race = Race::factory()->create();

    $this->actingAs($race->universe()->user)
        ->get(route('weekend.results', $race))
        ->assertRedirectToRoute('seasons.races.index', $race->season);
});

it('does not show the race result page for uncompleted races', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();

    $this->actingAs($season->universe->user)
        ->get(route('weekend.results', $race))
        ->assertRedirectToRoute('weekend.race', $race);
});

it('shows the race result page', function () {
    $season = Season::factory()->started()->create();
    PointSystem::factory()->for($season)->create();
    $race = Race::factory()->completed()->for($season)->create();
    RaceResult::factory()->for($race)->create();

    $this->actingAs($season->universe->user)
        ->get(route('weekend.results', $race))
        ->assertOk()
        ->assertComponent('RaceWeekend/Results')
        ->assertHasResource('results', (new GetRaceResults)->handle($race));
});
