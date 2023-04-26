<?php

use App\Actions\CalculateTieBreaker;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;

// Race results achieved by drivers, structured as array<driverOneResult: int, driverTwoResult: int>
$results = [
    [1, 2],
    [2, 1],
    [3, 2],
    [1, 5],
    [2, 1],
    [4, 3],
];

it('decides tie breakers based on finishing positions', function () use ($results) {
    $season = Season::factory()->create();
    [$teamOne, $teamTwo] = Entrant::factory(2)->for($season)->create();
    $driverOne = Racer::factory()->for($teamOne)->for($season)->create();
    $driverTwo = Racer::factory()->for($teamTwo)->for($season)->create();

    foreach ($results as $result) {
        [$driverOneResult, $driverTwoResult] = $result;
        RaceResult::factory()->for($season)->create([
            'entrant_id' => $driverOne->entrant_id,
            'racer_id' => $driverOne->id,
            'position' => $driverOneResult,
        ]);

        RaceResult::factory()->for($season)->create([
            'entrant_id' => $driverTwo->entrant_id,
            'racer_id' => $driverTwo->id,
            'position' => $driverTwoResult,
        ]);
    }

    $season->load('raceResults');
    $driverOne->load('raceResults');
    $driverTwo->load('raceResults');

    $this->assertEquals(1, (new CalculateTieBreaker($season))->handle($driverOne, $driverTwo));
    $this->assertEquals(1, (new CalculateTieBreaker($season))->handle($teamOne, $teamTwo));
});
