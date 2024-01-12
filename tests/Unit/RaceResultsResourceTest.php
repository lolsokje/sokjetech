<?php

use App\Http\Resources\Race\Results\RaceResultResource;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;

it('returns the correct race result', function () {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create();

    createRaceResult($race, $racer);

    $resource = (new RaceResultResource($race->raceResults()->first()))->toResponse(request())->getData(true);

    $driverRating = $racer->rating;
    $teamRating = $racer->entrant->rating;
    $engineRating = $racer->entrant->engine->rating;
    $totalRating = $driverRating + $teamRating + $engineRating;

    $this->assertEquals([
        'id' => 1,
        'driver' => [
            'first_name' => $racer->driver->first_name,
            'last_name' => $racer->driver->last_name,
            'number' => $racer->number,
        ],
        "ratings" => [
            "starting_bonus" => 60,
            "driver_rating" => $driverRating,
            "team_rating" => $teamRating,
            "engine_rating" => $engineRating,
            "total" => $totalRating,
            "starting_total" => $totalRating + 60,
            "driver_reliability" => $racer->reliability,
            "team_reliability" => $racer->entrant->reliability,
            "engine_reliability" => $racer->entrant->engine->reliability,
        ],
        "performance" => [
            "starting_position" => 1,
            "position" => 1,
            "position_change" => 0,
            "stints" => [
                0 => 1,
                1 => 2,
                2 => 3,
            ],
            "stints_total" => 6,
            "race_total" => $totalRating + 60,
            "fastest_lap_roll" => null,
            "dnf" => null,
            "fastest_lap" => false,
            "points" => 0,
        ],
        "team" => [
            "name" => $racer->entrant->full_name,
            "style_string" => $racer->entrant->style_string,
            "accent_colour" => $racer->entrant->accent_colour,
        ],
    ], $resource);
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
        'total' => $racer->rating + $racer->entrant->rating + $racer->entrant->engine->rating + 60,
    ]);
}
