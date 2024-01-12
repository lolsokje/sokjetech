<?php

use App\Actions\Races\Results\StoreQualifyingResultsAction;
use App\DataTransferObjects\RaceWeekend\QualifyingDriver;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertTrue;

it('stores qualifying results in the database', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);
});

// race IDs are cast to strings when fetched from the database, ensuring they're properly cast to integers
// when looking for results when updating results ensures no double qualifying results are created
it('casts the race ID to an integer', function () {
    $race = Race::factory()->create();
    $racers = Racer::factory(2)->for($race->season)->create();

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

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);

    postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 2))
        ->assertOk();

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);
});

test('a stored qualifying result stint has the correct format', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertOk();

    $firstResult = $race->qualifyingResults()->first();
    $runs = $firstResult->runs;

    assertCount(1, $runs);
    assertIsArray($runs);
    $firstStint = $runs[0];
    assertCount(1, $firstStint);
    assertIsArray($firstStint);
    assertIsInt($firstStint[array_key_first($firstStint)]);

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 2))
        ->assertOk();

    $firstResult = $race->fresh()->qualifyingResults()->first();
    $runs = $firstResult->runs;

    assertCount(1, $runs);
    assertIsArray($runs);
    $firstStint = $runs[0];
    assertCount(2, $firstStint);
    assertIsArray($firstStint);
    assertIsInt($firstStint[array_key_first($firstStint)]);

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 2, 2))
        ->assertOk();

    $firstResult = $race->fresh()->qualifyingResults()->first();
    $runs = $firstResult->runs;

    assertCount(2, $runs);
    assertIsArray($runs);
    $firstStint = $runs[0];
    assertCount(2, $firstStint);
    assertIsArray($firstStint);
    assertIsInt($firstStint[array_key_first($firstStint)]);
});

test('only universe owners can store qualifying results', function () {
    [$user, $drivers, $race] = prepareSeason();

    postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertForbidden();

    assertDatabaseCount('qualifying_results', 0);
    assertCount(0, $race->fresh()->qualifyingResults);

    actingAs(User::factory()->create())
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertForbidden();

    assertDatabaseCount('qualifying_results', 0);
    assertCount(0, $race->fresh()->qualifyingResults);

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertOk();

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->fresh()->qualifyingResults);
});

test('the driver position is correctly determined', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertOk();

    $results = $race->fresh()->qualifyingResults;

    foreach ($results as $key => $result) {
        assertEquals($key + 1, $result->position);
    }
});

it('marks qualifying as started once the first runs are stored', function () {
    [$user, $drivers, $race] = prepareSeason();

    assertFalse($race->qualifying_started);

    actingAs($user)
        ->post(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertOk();

    assertTrue($race->fresh()->qualifying_started);
});

it('does not update the race when an error occurs', function () {
    $this->withoutExceptionHandling();
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(2)->for($race->season)->create();

    $data = getDriverRuns($racers, 1, 1);

    $data['drivers'][0]['id'] = 1234;

    $this->actingAs($season->universe->user)
        ->post(route('weekend.qualifying.results.store', $race), $data);

    $this->assertFalse($race->qualifying_started);
    $this->assertCount(0, $race->qualifyingResults);
})->expectException(QueryException::class);

function getDriverRuns(Collection $drivers, ?int $sessionCount = 3, ?int $runCount = 3): array
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
            'result' => [
                'position' => $key + 1,
                'sessions' => $driverRuns,
            ],
        ];
    }

    $details = [];

    for ($sessionIndex = 0; $sessionIndex < $sessionCount; $sessionIndex++) {
        for ($runIndex = 0; $runIndex < $runCount; $runIndex++) {
            if (! isset($details[$sessionIndex])) {
                $details[$sessionIndex] = 0;
            }

            $details[$sessionIndex]++;
        }
    }

    return [
        'drivers' => $runs,
        'details' => $details,
    ];
}
