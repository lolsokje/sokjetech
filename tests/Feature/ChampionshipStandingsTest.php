<?php

use App\Jobs\CalculateDriverChampionshipStandingsJob;
use App\Jobs\CalculateTeamChampionshipStandingsJob;
use App\Models\PointSystem;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;
use App\Models\User;

it('dispatches the championship standings job when completing a race', function () {
    Queue::fake();

    $season = Season::factory()->create(['started' => true]);
    PointSystem::factory()->for($season)->create();
    $race = Race::factory()->for($season)->create();

    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->post(route('weekend.race.complete', $race));

    Queue::assertPushed(CalculateDriverChampionshipStandingsJob::class);
    Queue::assertPushed(CalculateTeamChampionshipStandingsJob::class);
});

it('calculates driver championship standings', function () {
    $season = Season::factory()->create();
    [$driverOne, $driverTwo] = Racer::factory(2)->for($season)->create();

    $results = [
        $driverOne->id => [ // 51 total points, P2
            [2, 18],
            [2, 18],
            [3, 15],
        ],
        $driverTwo->id => [ // 58 total points, P1
            [1, 25],
            [2, 18],
            [3, 15],
        ],
    ];

    storeRaceResults($season, $results);

    (new CalculateDriverChampionshipStandingsJob($season))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 2);
    $this->assertDatabaseHas('driver_championship_standings', [
        'racer_id' => $driverOne->id,
        'points' => 51,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('driver_championship_standings', [
        'racer_id' => $driverTwo->id,
        'points' => 58,
        'position' => 1,
    ]);
});

it('correctly determines tie breakers based on position', function () {
    $season = Season::factory()->create();
    [$driverOne, $driverTwo] = Racer::factory(2)->for($season)->create();

    $results = [
        $driverOne->id => [ // 4 total points, P2 because of worse top result
            [10, 1],
            [10, 1],
            [9, 2],
        ],
        $driverTwo->id => [ // 4 total points, P1 because of better top result
            [11, 0],
            [11, 0],
            [8, 4],
        ],
    ];

    storeRaceResults($season, $results);

    (new CalculateDriverChampionshipStandingsJob($season))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 2);

    $this->assertDatabaseHas('driver_championship_standings', [
        'racer_id' => $driverOne->id,
        'points' => 4,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('driver_championship_standings', [
        'racer_id' => $driverTwo->id,
        'points' => 4,
        'position' => 1,
    ]);
});

it('overwrites existing standings', function () {
    $season = Season::factory()->create();
    RaceResult::factory(3)->for($season)->create();

    (new CalculateDriverChampionshipStandingsJob($season))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 3);

    (new CalculateDriverChampionshipStandingsJob($season))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 3);
});

function storeRaceResults(Season $season, array $results): Season
{
    foreach ($results as $id => $driverResults) {
        foreach ($driverResults as $result) {
            [$position, $points] = $result;
            RaceResult::factory()->for($season)->create([
                'racer_id' => $id,
                'position' => $position,
                'points' => $points,
            ]);
        }
    }

    return $season;
}
