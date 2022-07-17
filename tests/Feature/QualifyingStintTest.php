<?php

use App\Models\User;
use Illuminate\Support\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsInt;

it('stores qualifying results in the database', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertRedirect(route('weekend.qualifying', [$race]));

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);
});

it('overwrites existing qualifying results for the same race', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertRedirect(route('weekend.qualifying', [$race]));

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);

    postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 2))
        ->assertRedirect(route('weekend.qualifying', [$race]));

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->qualifyingResults);
});

test('a stored qualifying result stint has the correct format', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers, 1, 1))
        ->assertRedirect(route('weekend.qualifying', [$race]));

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
        ->assertRedirect(route('weekend.qualifying', [$race]));

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
        ->assertRedirect(route('weekend.qualifying', [$race]));

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
        ->assertRedirect(route('weekend.qualifying', [$race]));

    assertDatabaseCount('qualifying_results', 5);
    assertCount(5, $race->fresh()->qualifyingResults);
});

test('the driver position is correctly determined', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->postJson(route('weekend.qualifying.results.store', [$race]), getDriverRuns($drivers))
        ->assertRedirect(route('weekend.qualifying', [$race]));

    $results = $race->fresh()->qualifyingResults;

    foreach ($results as $key => $result) {
        assertEquals($key + 1, $result->position);
    }
});

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
            'position' => $key + 1,
            'runs' => $driverRuns,
        ];
    }

    $details = [];

    for ($sessionIndex = 0; $sessionIndex < $sessionCount; $sessionIndex++) {
        for ($runIndex = 0; $runIndex < $runCount; $runIndex++) {
            if (!isset($details[$sessionIndex])) {
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
