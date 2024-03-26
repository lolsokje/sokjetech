<?php

use App\Actions\Races\Results\StoreQualifyingResultsAction;
use App\DataTransferObjects\RaceWeekend\QualifyingDriver;
use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Collection;

it('stores qualifying results in the database', function () {
    [$user, $drivers, $race] = prepareSeason();

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    $this->assertDatabaseCount('qualifying_results', 5);
    $this->assertCount(5, $race->qualifyingResults);
});

it('updates the qualifying details for the race', function () {
    /** @var Race $race */
    [$user, $drivers, $race] = prepareSeason(1);

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    $this->assertEquals(1, $race->qualifying_session);
    $this->assertEquals(1, $race->qualifying_run);
});

// race IDs are cast to strings when fetched from the database, ensuring they're properly cast to integers
// when looking for results when updating results ensures no double qualifying results are created
it('casts the race ID to an integer', function () {
    $race = Race::factory()->create();
    $racers = Racer::factory(2)->for($race->season)->create();

    foreach ($racers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $drivers = collect(getDriverRuns($racers, 1, 1)['drivers'])->map(
        fn (array $driver) => QualifyingDriver::fromRequest($driver),
    );

    $action = new StoreQualifyingResultsAction;

    $action->handle($drivers, $race->id, $race->season_id);
    $action->handle($drivers, $race->id, $race->season_id);

    $this->assertCount(2, $race->qualifyingResults);
});

it('overwrites existing qualifying results for the same race', function () {
    [$user, $drivers, $race] = prepareSeason();

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($race)->for($driver)->create();
    }

    $this->assertDatabaseCount('qualifying_results', 5);
    $this->assertCount(5, $race->qualifyingResults);

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 2))
        ->assertOk();

    $this->assertDatabaseCount('qualifying_results', 5);
    $this->assertCount(5, $race->qualifyingResults);
});

test('a stored qualifying result stint has the correct format', function () {
    [$user, $drivers, $race] = prepareSeason();

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    $firstResult = $race->qualifyingResults()->first();
    $runs = $firstResult->runs;

    $this->assertCount(1, $runs);
    $this->assertIsArray($runs);
    $firstStint = $runs[0];
    $this->assertCount(1, $firstStint);
    $this->assertIsArray($firstStint);
    $this->assertIsInt($firstStint[array_key_first($firstStint)]);

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 2))
        ->assertOk();

    $firstResult = $race->fresh()->qualifyingResults()->first();
    $runs = $firstResult->runs;

    $this->assertCount(1, $runs);
    $this->assertIsArray($runs);
    $firstStint = $runs[0];
    $this->assertCount(2, $firstStint);
    $this->assertIsArray($firstStint);
    $this->assertIsInt($firstStint[array_key_first($firstStint)]);

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 2, 2))
        ->assertOk();

    $firstResult = $race->fresh()->qualifyingResults()->first();
    $runs = $firstResult->runs;

    $this->assertCount(2, $runs);
    $this->assertIsArray($runs);
    $firstStint = $runs[0];
    $this->assertCount(2, $firstStint);
    $this->assertIsArray($firstStint);
    $this->assertIsInt($firstStint[array_key_first($firstStint)]);
});

test('only universe owners can store qualifying results', function () {
    [$user, $drivers, $race] = prepareSeason();

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $driverRuns = getDriverRuns($drivers, 1, 1);

    $this->postJson(route('weekend.qualifying.results.store', [$race]), $driverRuns)
        ->assertForbidden();

    $firstResult = $race->qualifyingResults->first();
    $this->assertCount(0, json_decode($firstResult->runs));

    $this->actingAs(User::factory()->create())
        ->postJson(route('weekend.qualifying.results.store', [$race]), $driverRuns)
        ->assertForbidden();

    $firstResult->refresh();
    $this->assertCount(0, json_decode($firstResult->runs));

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), $driverRuns)
        ->assertOk();

    $firstResult->refresh();
    $this->assertCount(1, $firstResult->runs);
});

test('the driver position is correctly determined', function () {
    [$user, $drivers, $race] = prepareSeason();

    foreach ($drivers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $this->actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertOk();

    $results = $race->fresh()->qualifyingResults;

    foreach ($results as $key => $result) {
        $this->assertEquals($key + 1, $result->position);
    }
});

it('does not update the race when an error occurs', function () {
    $this->withoutExceptionHandling();
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(2)->for($race->season)->create();

    foreach ($racers as $driver) {
        QualifyingResult::factory()->for($driver)->for($race)->create();
    }

    $data = getDriverRuns($racers, 1, 1);

    $data['drivers'][0]['id'] = 1234;

    $this->actingAs($season->universe->user)
        ->post(route('weekend.qualifying.results.store', $race), $data);

    $this->assertFalse($race->qualifying_started);
    $this->assertCount(0, $race->qualifyingResults);
})->expectException(Error::class);

function getDriverRuns(Collection $drivers, ?int $sessionCount = 1, ?int $runCount = 1): array
{
    $runs = [];

    foreach ($drivers as $key => $driver) {
        $driverRuns = [];

        for ($i = 0; $i < $sessionCount; $i++) {
            for ($j = 0; $j < $runCount; $j++) {
                $driverRuns["$i"]["$j"] = rand(5, 30);
            }
        }
        $runs[] = [
            'id' => $driver->id,
            'entrant_id' => $driver->entrant->id,
            'ratings' => [
                'driver_rating' => $driver->rating,
                'team_rating' => $driver->entrant->rating,
                'engine_rating' => $driver->entrant->engine->rating,
            ],
            'performance' => [
                'position' => $key + 1,
                'sessions' => $driverRuns,
            ],
        ];
    }

    return [
        'drivers' => $runs,
        'details' => [
            'current_session' => $sessionCount,
            'current_run' => $runCount,
        ],
    ];
}
