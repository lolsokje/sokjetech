<?php

use App\Actions\Season\Development\History\GetDriverDevelopmentHistory;
use App\Models\DriverDevelopmentHistory;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;

it('works', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $driver = Racer::factory()->for($season)->create([$component => 30]);

    foreach ($races as $race) {
        DriverDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($driver)
            ->create([
                'component' => $component,
                'initial' => $driver->getComponentRating($component),
                'development' => 1,
            ]);

        $driver->update([$component => $driver->getComponentRating($component) + 1]);
    }

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $result = $results[0];

    $this->assertEquals($driver->driver->full_name, $result->label);
    $this->assertEquals([31, 32, 33], $result->history);
    $this->assertEquals($driver->entrant->accent_colour, $result->accent);
    $this->assertFalse($result->dash);
})->with('driver components');

it('does not include drivers without development history', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    $drivers = Racer::factory(2)->for($season)->create();

    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($drivers[1])
        ->for($races[0])
        ->create([
            'component' => $component,
        ]);

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertCount(1, $results);
    $this->assertEquals($drivers[1]->driver->full_name, $results[0]->label);
})->with('driver components');

it('sets the value to the previous value for races without dev', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $driver = Racer::factory()->for($season)->create([$component => 30]);

    foreach ([$races[0], $races[2]] as $race) {
        DriverDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($driver)
            ->create([
                'component' => $component,
                'initial' => $driver->getComponentRating($component),
                'development' => 1,
            ]);

        $driver->update([$component => $driver->getComponentRating($component) + 1]);
    }

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([31, 31, 32], $results[0]->history);
})->with('driver components');

it('sets the value to null for uncompleted races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->completed()->create(['order' => 1]);
    Race::factory(2)->for($season)->sequence(
        ['order' => 2],
        ['order' => 3],
    )->create();
    $driver = Racer::factory()->for($season)->create([$component => 30]);

    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($race)
        ->for($driver)
        ->create([
            'component' => $component,
            'initial' => $driver->getComponentRating($component),
            'development' => 1,
        ]);

    $driver->update([$component => $driver->getComponentRating($component) + 1]);

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $season->refresh()->races,
        component: $component,
    );

    $history = $results[0]->history;

    $this->assertEquals(31, $history[0]);
    $this->assertEquals(31, $history[1]);
    $this->assertNull($history[2]);
})->with('driver components');

it('returns the value of the first dev round for all races before the first round', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $driver = Racer::factory()->for($season)->create([$component => 30]);

    // First dev round happens before the third race of the season
    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($races[2])
        ->for($driver)
        ->create([
            'component' => $component,
            'initial' => $driver->getComponentRating($component),
            'development' => 1,
        ]);

    $driver->update([$component => $driver->getComponentRating($component) + 1]);

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([30, 30, 31], $results[0]->history);
})->with('driver components');

it('returns nothing when no development has taken place yet', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    Racer::factory(2)->for($season)->create();

    $results = (new GetDriverDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([], $results);
})->with('driver components');
