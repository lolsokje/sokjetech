<?php

use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;

it('marks qualifying as started and creates empty qualifying results', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();
    Racer::factory(3)->for($season)->create();

    $this->actingAs($season->universe->user)
        ->post(route('weekend.qualifying.start', $race))
        ->assertRedirectToRoute('weekend.qualifying', $race);

    $race->refresh();

    $this->assertTrue($race->qualifying_started);
    $this->assertCount(3, $race->qualifyingResults);
});

it('redirects to the qualifying page when qualifying has already started', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create(['qualifying_started' => true]);
    $racers = Racer::factory(3)->for($season)->create();

    foreach ($racers as $racer) {
        QualifyingResult::factory()->for($season)->for($race)->for($racer)->for($racer->entrant)->create();
    }

    $this->assertCount(3, $race->qualifyingResults);

    $this->actingAs($season->universe->user)
        ->post(route('weekend.qualifying.start', $race))
        ->assertRedirectToRoute('weekend.qualifying', $race);

    $this->assertCount(3, $race->refresh()->qualifyingResults);
});

test('only authorised users can start race weekends', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();
    Racer::factory(3)->for($season)->create();

    $this->post(route('weekend.qualifying.start', $race))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('weekend.qualifying.start', $race))
        ->assertForbidden();

    $this->assertFalse($race->refresh()->qualifying_started);
    $this->assertCount(0, $race->qualifyingResults);
});

it('stores ratings when creating qualifying results', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(3)->for($season)->create();

    $this->actingAs($season->universe->user)
        ->post(route('weekend.qualifying.start', $race));

    foreach ($racers as $racer) {
        $result = $race->qualifyingResults()->where('racer_id', $racer->id)->first();

        $this->assertEquals($racer->rating, $result->driver_rating);
        $this->assertEquals($racer->entrant->rating, $result->team_rating);
        $this->assertEquals($racer->entrant->engine->rating, $result->engine_rating);
    }
});
