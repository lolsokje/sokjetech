<?php

use App\Enums\DistanceType;
use App\Enums\RaceType;
use App\Models\Race;

it('returns the correct readable string', function (int $duration, RaceType $raceType, string $expected) {
    $race = Race::factory()->create([
        'duration' => $duration,
        'race_type' => $raceType->value,
    ]);

    $this->assertEquals($expected, $race->raceDuration()->readable());
})->with([
    [50, RaceType::LAP, '50'],
    [750, RaceType::TIME, '12h30m'],
    [100000, RaceType::DISTANCE, '100km/62m'],
]);

it('returns the correct postfix', function (RaceType $raceType, string $expected) {
    $race = Race::factory()->create([
        'race_type' => $raceType->value,
    ]);

    $this->assertEquals($expected, $race->raceDuration()->postfix());
})->with([
    [RaceType::LAP, 'laps'],
    [RaceType::TIME, ''],
    [RaceType::DISTANCE, ''],
]);

it('returns the correct editable format', function (
    int $duration,
    RaceType $raceType,
    string|int|array $expected,
    ?DistanceType $distanceType = DistanceType::KM,
) {
    $race = Race::factory()->create([
        'duration' => $duration,
        'race_type' => $raceType->value,
        'distance_type' => $distanceType?->value,
    ]);

    $this->assertEquals($expected, $race->raceDuration()->editable());
})->with([
    [50, RaceType::LAP, '50'],
    [750, RaceType::TIME, [12, 30]],
    [100000, RaceType::DISTANCE, 100],
    [100000, RaceType::DISTANCE, 62, DistanceType::M],
]);
