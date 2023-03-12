<?php

use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\Actions\Season\Standings\CalculateTeamChampionshipStandingsAction;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\Entrant;
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

    Queue::assertPushed(CalculateChampionshipStandingsJob::class, 2);
});

it('calculates driver championship standings', function () {
    $season = Season::factory()->create();
    [$driverOne, $driverTwo] = Racer::factory(2)->for($season)->create();

    $results = [
        $driverOne->entrant_id => [
            $driverOne->id => [ // 51 total points, P2
                [2, 18],
                [2, 18],
                [3, 15],
            ],
        ],
        $driverTwo->entrant_id => [
            $driverTwo->id => [ // 58 total points, P1
                [1, 25],
                [2, 18],
                [3, 15],
            ],
        ],
    ];

    storeRaceResults($season, $results);

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season),
    ))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 2);
    $this->assertDatabaseHas('driver_championship_standings', [
        'driver_id' => $driverOne->entrant->allRacers->first()->driver_id,
        'points' => 51,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('driver_championship_standings', [
        'driver_id' => $driverTwo->entrant->allRacers->first()->driver_id,
        'points' => 58,
        'position' => 1,
    ]);
});

it('calculates team championship standings', function () {
    $season = Season::factory()->create();
    [$teamOne, $teamTwo] = Entrant::factory(2)->for($season)->create();
    [$driverOne, $driverTwo] = Racer::factory(2)->for($teamOne)->for($season)->create();
    [$driverThree, $driverFour] = Racer::factory(2)->for($teamTwo)->for($season)->create();

    $results = [
        $teamOne->id => [ // 60 points, P2
            $driverOne->id => [
                [2, 18],
                [3, 15],
            ],
            $driverTwo->id => [
                [3, 15],
                [4, 12],
            ],
        ],
        $teamTwo->id => [ // 80 points, P1
            $driverThree->id => [
                [1, 25],
                [2, 18],
            ],
            $driverFour->id => [
                [4, 12],
                [1, 25],
            ],
        ],
    ];

    storeRaceResults($season, $results);

    (new CalculateChampionshipStandingsJob(
        new CalculateTeamChampionshipStandingsAction($season),
    ))->handle();

    $this->assertDatabaseCount('team_championship_standings', 2);

    $this->assertDatabaseHas('team_championship_standings', [
        'entrant_id' => $teamOne->id,
        'points' => 60,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('team_championship_standings', [
        'entrant_id' => $teamTwo->id,
        'points' => 80,
        'position' => 1,
    ]);
});

it('correctly determines tie breakers based on position', function () {
    $season = Season::factory()->create();
    [$teamOne, $teamTwo] = Entrant::factory(2)->for($season)->create();
    $driverOne = Racer::factory()->for($teamOne)->for($season)->create();
    $driverTwo = Racer::factory()->for($teamTwo)->for($season)->create();

    $results = [
        $driverOne->entrant_id => [
            $driverOne->id => [ // 4 total points, P2 because of worse top result
                [10, 1],
                [10, 1],
                [9, 2],
            ],
        ],
        $driverTwo->entrant_id => [
            $driverTwo->id => [ // 4 total points, P1 because of better top result
                [11, 0],
                [11, 0],
                [8, 4],
            ],
        ],
    ];

    storeRaceResults($season, $results);

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season),
    ))->handle();

    (new CalculateChampionshipStandingsJob(
        new CalculateTeamChampionshipStandingsAction($season)
    ))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 2);
    $this->assertDatabaseCount('team_championship_standings', 2);

    $this->assertDatabaseHas('driver_championship_standings', [
        'driver_id' => $driverOne->entrant->allRacers->first()->driver_id,
        'points' => 4,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('driver_championship_standings', [
        'driver_id' => $driverTwo->entrant->allRacers->first()->driver_id,
        'points' => 4,
        'position' => 1,
    ]);

    $this->assertDatabaseHas('team_championship_standings', [
        'entrant_id' => $teamOne->id,
        'points' => 4,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('team_championship_standings', [
        'entrant_id' => $teamTwo->id,
        'points' => 4,
        'position' => 1,
    ]);
});

it('overwrites existing standings', function () {
    $season = Season::factory()->create();
    RaceResult::factory(3)->for($season)->create();

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season),
    ))->handle();
    (new CalculateChampionshipStandingsJob(
        new CalculateTeamChampionshipStandingsAction($season),
    ))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 3);
    $this->assertDatabaseCount('team_championship_standings', 3);

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season),
    ))->handle();
    (new CalculateChampionshipStandingsJob(
        new CalculateTeamChampionshipStandingsAction($season),
    ))->handle();

    $this->assertDatabaseCount('driver_championship_standings', 3);
    $this->assertDatabaseCount('team_championship_standings', 3);
});

function storeRaceResults(Season $season, array $results): Season
{
    foreach ($results as $teamId => $teamResults) {
        foreach ($teamResults as $driverId => $driverResults) {
            foreach ($driverResults as $result) {
                [$position, $points] = $result;
                RaceResult::factory()->for($season)->create([
                    'entrant_id' => $teamId,
                    'racer_id' => $driverId,
                    'position' => $position,
                    'points' => $points,
                ]);
            }
        }
    }

    return $season;
}
