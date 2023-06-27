<?php

use App\Enums\RaceType;
use App\Models\Race;
use App\Support\Formatters\RaceDurationFormatter;

it('converts race duration to hours and minutes', function (int $hours, int $minutes) {
    $totalDuration = ($hours * 60) + $minutes;

    $this->assertEquals([
        $hours,
        $minutes,
    ], RaceDurationFormatter::toHoursAndMinutes($totalDuration));
})->with([
    [7, 46],
    [24, 0],
    [0, 39],
]);

it('converts laps based duration to readable format', function () {
    $race = Race::factory()->create([
        'duration' => 50,
        'race_type' => RaceType::LAP->value,
    ]);

    $this->assertEquals('50', $race->raceDuration()->readable());
});

it('converts time based duration to readable format', function () {
    $race = Race::factory()->create([
        'duration' => 750,
        'race_type' => RaceType::TIME->value,
    ]);

    $this->assertEquals('12h30m', $race->raceDuration()->readable());
});

it('converts distance based duration to readable format', function () {
    $race = Race::factory()->create([
        'duration' => 305000,
        'race_type' => RaceType::DISTANCE->value,
    ]);

    $this->assertEquals('305km/190m', $race->raceDuration()->readable());
});
