<?php

use App\Actions\Races\Results\UpdateRaceResults;
use App\DataTransferObjects\Race\Result\RaceResultCollection;
use App\Models\Race;
use App\Models\RaceResult;
use App\Models\Season;

test('a race result must exist before results can be updated', function () {
    $season = Season::factory()->create(['started' => true]);
    $race = Race::factory()->for($season)->create();

    $this->actingAs($season->universe->user)
        ->put(route('weekend.race.results.update', $race), getRaceResultData())
        ->assertSessionHasErrors('results.0.id');
});

test('qualifying must have completed before results can be updated', function () {
    $season = Season::factory()->create(['started' => true]);
    $race = Race::factory()->for($season)->create();
    $result = RaceResult::factory()->for($race)->create();

    $this->actingAs($season->universe->user)
        ->put(route('weekend.race.results.update', $race), getRaceResultData($result))
        ->assertRedirectToRoute('weekend.qualifying', $race);
});

it('updates race details', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->raceReady()->create(['started' => false]);
    $result = RaceResult::factory()->for($race)->create();

    $this->actingAs($season->universe->user)
        ->put(route('weekend.race.results.update', $race), getRaceResultData($result))
        ->assertOk();

    $race->refresh();

    $this->assertTrue($race->started);
});

test('results can be updated', function () {
    $season = Season::factory()->started()->create();
    $race = Race::factory()->for($season)->create(['qualifying_completed' => true]);
    $result = RaceResult::factory()->for($race)->create([
        'position' => 3,
        'stints' => [],
        'total' => 0,
    ]);

    $action = new UpdateRaceResults;

    $action->handle(
        new RaceResultCollection([
            new \App\DataTransferObjects\Race\Result\RaceResult(
                id: $result->id,
                position: 1,
                total: 6,
                stints: [1],
            ),
        ]),
    );

    $result->refresh();

    $this->assertEquals(1, $result->position);
    $this->assertEquals([1], $result->stints);
    $this->assertEquals(6, $result->total);
});

it('updates race results', function () {
    $season = Season::factory()->create(['started' => true]);
    $race = Race::factory()->for($season)->create(['qualifying_completed' => true]);
    $result = RaceResult::factory()->for($race)->create([
        'position' => 3,
        'stints' => [],
        'total' => 0,
    ]);

    $this->actingAs($season->universe->user)
        ->put(route('weekend.race.results.update', $race), getRaceResultData($result))
        ->assertOk();

    $result->refresh();

    $this->assertEquals(1, $result->position);
    $this->assertEquals([1], $result->stints);
    $this->assertEquals(6, $result->total);
});

function getRaceResultData(?RaceResult $raceResult = null): array
{
    return [
        'current_lap' => 1,
        'results' => [
            [
                'id' => $raceResult?->id ?? 1,
                'position' => 1,
                'stints' => [1],
                'total' => 6,
            ],
        ],
    ];
}
