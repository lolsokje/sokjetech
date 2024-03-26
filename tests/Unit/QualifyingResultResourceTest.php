<?php

use App\Actions\GetQualifyingResults;
use App\Http\Resources\QualifyingResultResource;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;

it('returns all qualifying participants if qualifying has started', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(5)->for($season)->create();

    $racers->each(fn (Racer $racer) => createQualifyingResult($race, $racer));

    $racers->last()->update(['active' => false]);

    $this->assertCount(5, $season->drivers);
    $this->assertCount(4, $season->activeRacers);

    $results = (new GetQualifyingResults)->handle($race);

    $this->assertCount(5, $results);
});

it('combines drivers with their qualifying result', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create();

    createQualifyingResult($race, $racer);

    $this->assertDatabaseCount('qualifying_results', 1);

    /** @var QualifyingResultResource[] $qualifyingResults */
    $qualifyingResults = (new GetQualifyingResults)->handle($race);
    $driverResults = $qualifyingResults[0]->toArray(request());

    $this->assertEquals(
        QualifyingResultResource::make($race->qualifyingResults->first())->toArray(request()),
        $driverResults,
    );
});

function createQualifyingResult(Race $race, Racer $racer): void
{
    $race->qualifyingResults()->create([
        'season_id' => $race->season_id,
        'racer_id' => $racer->id,
        'entrant_id' => $racer->entrant_id,
        'driver_rating' => $racer->rating,
        'team_rating' => $racer->entrant->rating,
        'engine_rating' => $racer->entrant->engine->rating,
        'position' => 1,
        'runs' => [
            [10, 15],
            [20, 25],
        ],
    ]);
}
