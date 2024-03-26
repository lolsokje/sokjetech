<?php

use App\Http\Resources\Race\RaceResource;
use App\Models\QualifyingFormats\SingleSession;
use App\Models\Race;
use App\Models\Season;

it('shows the qualifying page', function () {
    $season = Season::factory()->started()->create();
    $format = SingleSession::factory()->create();
    $format->season()->save($season);
    $race = Race::factory()->for($season)->create();

    $this->actingAs($season->universe->user)
        ->get(route('weekend.qualifying', $race))
        ->assertOk()
        ->assertComponent('RaceWeekend/Qualifying')
        ->assertHasResource('race', RaceResource::make($race->load('season.qualifyingFormat')));
});
