<?php

use App\Actions\Races\CalculatePointsScored;
use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\Race;
use App\Models\RaceResult;
use App\Models\Season;

it('calculates points scored', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();

    $points = [
        1 => 5,
        2 => 3,
        3 => 1,
    ];

    $system = PointSystem::factory()->for($season)->create([
        'pole_position_point_awarded' => true,
        'pole_position_point_amount' => 2,
        'fastest_lap_point_awarded' => true,
        'fastest_lap_point_amount' => 4,
    ]);

    foreach ($points as $position => $pointAmount) {
        PointDistribution::factory()->for($system)->create([
            'position' => $position,
            'points' => $pointAmount,
        ]);
    }

    $results = [
        [
            'starting_position' => 1, // 2
            'position' => 1, // 5
            'dnf' => null,
            'fastest_lap' => false,
            'points' => 7,
        ],
        [
            'starting_position' => 2, // 0
            'position' => 2, // 3
            'dnf' => null,
            'fastest_lap' => false,
            'points' => 3,
        ],
        [
            'starting_position' => 3, // 0,
            'position' => 3, // 1
            'dnf' => null,
            'fastest_lap' => true,
            'points' => 5,
        ],
        [
            'starting_position' => 4,
            'position' => 4,
            'dnf' => 'DNF',
            'fastest_lap' => false,
            'points' => 0,
        ],
    ];

    foreach ($results as $result) {
        RaceResult::factory()->for($race)->create([
            'starting_position' => $result['starting_position'],
            'position' => $result['position'],
            'fastest_lap' => $result['fastest_lap'],
            'dnf' => $result['dnf'],
        ]);
    }

    (new CalculatePointsScored($race))->handle();

    foreach ($race->raceResults as $key => $raceResult) {
        $result = $results[$key];

        $this->assertEquals($raceResult->points, $result['points']);
    }
});
