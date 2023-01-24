<?php

use App\Actions\GetRaceResults;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;

it('returns all active racers if a race weekend has not started', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(5)->for($season)->create();

    $racers->last()->update(['active' => false]);

    $results = (new GetRaceResults())->handle($race);

    $this->assertCount(4, $results);

    foreach ($results as $result) {
        $this->assertTrue(Racer::find($result['id'])->active);
    }
});

it('returns all race participants if a race weekend has started', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racers = Racer::factory(5)->for($season)->create();

    $racers->each(fn (Racer $racer) => createRaceResult($race, $racer));

    $racers->last()->update(['active' => false]);

    $this->assertCount(5, $season->drivers);
    $this->assertCount(4, $season->activeRacers);

    $results = (new GetRaceResults())->handle($race);

    $this->assertCount(5, $results);
});

it('combines drivers with their race result', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create();

    createRaceResult($race, $racer);

    $this->assertDatabaseCount('race_results', 1);

    $raceResults = (new GetRaceResults())->handle($race);
    $driverResult = $raceResults[0];

    $driverRating = $racer->rating;
    $teamRating = $racer->entrant->rating;
    $engineRating = $racer->entrant->engine->rating;

    $this->assertEquals([
        'id' => $racer->id,
        'entrant_id' => $racer->entrant_id,
        'full_name' => $racer->driver->full_name,
        'number' => $racer->number,
        'ratings' => [
            'driver_rating' => $driverRating,
            'team_rating' => $teamRating,
            'engine_rating' => $engineRating,
            'total_rating' => $driverRating + $teamRating + $engineRating,
            'driver_reliability' => $racer->reliability,
            'team_reliability' => $racer->entrant->reliability,
            'engine_reliability' => $racer->entrant->engine->reliability,
        ],
        'team' => [
            'team_name' => $racer->entrant->full_name,
            'short_team_name' => $racer->entrant->short_name,
            'style_string' => $racer->entrant->style_string,
            'primary_colour' => $racer->entrant->primary_colour,
            'secondary_colour' => $racer->entrant->secondary_colour,
            'accent_colour' => $racer->entrant->accent_colour,
        ],
        'result' => [
            'starting_position' => 1,
            'bonus' => 60,
            'position' => 1,
            'dnf' => null,
            'fastest_lap_roll' => null,
            'fastest_lap' => null,
            'stints' => [1, 2, 3],
        ],
    ], $driverResult);
});

function createRaceResult(Race $race, Racer $racer): void
{
    $race->raceResults()->create([
        'season_id' => $race->season_id,
        'racer_id' => $racer->id,
        'entrant_id' => $racer->entrant_id,
        'driver_rating' => $racer->rating,
        'team_rating' => $racer->entrant->rating,
        'engine_rating' => $racer->entrant->engine->rating,
        'starting_position' => 1,
        'starting_bonus' => 60,
        'position' => 1,
        'stints' => [1, 2, 3],
    ]);
}
