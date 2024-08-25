<?php

use App\Actions\Season\Development\History\GetEngineDevelopmentHistory;
use App\Models\EngineDevelopmentHistory;
use App\Models\EngineSeason;
use App\Models\Race;
use App\Models\Season;

it('works', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([$component => 30]);

    foreach ($races as $race) {
        EngineDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($engine)
            ->create([
                'component' => $component,
                'initial' => $engine->getComponentRating($component),
                'development' => 1,
            ]);

        $engine->update([$component => $engine->getComponentRating($component) + 1]);
    }

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $result = $results[0];

    $this->assertEquals($engine->name, $result->label);
    $this->assertEquals([31, 32, 33], $result->history);
    $this->assertEquals('', $result->accent);
    $this->assertFalse($result->dash);
})->with('engine components');

it('does not include engines without development history', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    $engines = EngineSeason::factory(2)->for($season)->create();

    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($engines[1])
        ->for($races[0])
        ->create([
            'component' => $component,
        ]);

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertCount(1, $results);
    $this->assertEquals($engines[1]->name, $results[0]->label);
})->with('engine components');

it('sets the value to the previous value for races without dev', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([$component => 30]);

    foreach ([$races[0], $races[2]] as $race) {
        EngineDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($engine)
            ->create([
                'component' => $component,
                'initial' => $engine->getComponentRating($component),
                'development' => 1,
            ]);

        $engine->update([$component => $engine->getComponentRating($component) + 1]);
    }

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([31, 31, 32], $results[0]->history);
})->with('engine components');

it('sets the value to null for uncompleted races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->completed()->create(['order' => 1]);
    Race::factory(2)->for($season)->sequence(
        ['order' => 2],
        ['order' => 3],
    )->create();
    $engine = EngineSeason::factory()->for($season)->create([$component => 30]);

    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($race)
        ->for($engine)
        ->create([
            'component' => $component,
            'initial' => $engine->getComponentRating($component),
            'development' => 1,
        ]);

    $engine->update([$component => $engine->getComponentRating($component) + 1]);

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $season->refresh()->races,
        component: $component,
    );

    $history = $results[0]->history;

    $this->assertEquals(31, $history[0]);
    $this->assertEquals(31, $history[1]);
    $this->assertNull($history[2]);
})->with('engine components');

it('returns the value of the first dev round for all races before the first round', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([$component => 30]);

    // First dev round happens before the third race of the season
    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($races[2])
        ->for($engine)
        ->create([
            'component' => $component,
            'initial' => $engine->getComponentRating($component),
            'development' => 1,
        ]);

    $engine->update([$component => $engine->getComponentRating($component) + 1]);

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([30, 30, 31], $results[0]->history);
})->with('engine components');

it('returns nothing when no development has taken place yet', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    EngineSeason::factory(2)->for($season)->create();

    $results = (new GetEngineDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([], $results);
})->with('engine components');
