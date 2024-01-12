<?php

use App\Actions\Races\Results\CreateRaceResultsAction;
use App\Models\QualifyingResult;
use App\Models\Race;

it('creates race results for all qualifying results', function () {
    $race = Race::factory()->create();
    QualifyingResult::factory(3)->for($race)->create();

    (new CreateRaceResultsAction)->handle($race);

    $this->assertDatabaseCount('race_results', 3);
    $this->assertCount(3, $race->raceResults);
});

it('correctly calculates the starting bonus', function () {
    $race = Race::factory()->create();
    QualifyingResult::factory(3)->for($race)->sequence(
        ['position' => 1],
        ['position' => 2],
        ['position' => 3],
    )->create();

    (new CreateRaceResultsAction)->handle($race);

    $this->assertDatabaseHas('race_results', ['starting_position' => 1, 'starting_bonus' => 9]);
    $this->assertDatabaseHas('race_results', ['starting_position' => 2, 'starting_bonus' => 6]);
    $this->assertDatabaseHas('race_results', ['starting_position' => 3, 'starting_bonus' => 3]);
});

it('correctly stores ratings', function () {
    $race = Race::factory()->create();
    $result = QualifyingResult::factory()->for($race)->create(['position' => 1]);

    (new CreateRaceResultsAction)->handle($race);

    $this->assertDatabaseHas('race_results', [
        'starting_position' => 1,
        'driver_rating' => $result->driver_rating,
        'team_rating' => $result->team_rating,
        'engine_rating' => $result->engine_rating,
        'total' => $result->driver_rating + $result->team_rating + $result->engine_rating + 3,
    ]);

    $raceResult = $race->raceResults->first();

    $this->assertNotNull($raceResult->driver_rating);
    $this->assertNotNull($raceResult->team_rating);
});
