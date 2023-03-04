<?php

use App\Actions\CalculateDriverTieBreaker;
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
    [$driverOne, $driverTwo] = Racer::factory(2)->for($season)->create();

    foreach ($results as $result) {
        [$driverOneResult, $driverTwoResult] = $result;
        RaceResult::factory()->for($season)->create([
            'racer_id' => $driverOne->id,
            'position' => $driverOneResult,
        ]);

        RaceResult::factory()->for($season)->create([
            'racer_id' => $driverTwo->id,
            'position' => $driverTwoResult,
        ]);
    }

    $season->load('raceResults');
    $driverOne->load('raceResults');
    $driverTwo->load('raceResults');

    $this->assertEquals(1, (new CalculateDriverTieBreaker($season))->handle($driverOne, $driverTwo));
});
