<?php

use App\Actions\Season\Development\StoreEngineDevelopment;
use App\Models\EngineDevelopmentHistory;
use App\Models\EngineSeason;
use App\Models\Race;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

const BASE_ENGINE_RATING = 20;
const BASE_ENGINE_DEV = 4;
const HALF_ENGINE_DEV = BASE_ENGINE_DEV / 2;

it('creates new engine development history records when no history exists', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([
        $component => BASE_ENGINE_RATING,
    ]);

    $entities = [generateDevelopmentEntity($engine, $component, BASE_ENGINE_DEV)];

    // Updates the rating to BASE_ENGINE_RATING + BASE_ENGINE_DEV
    (new StoreEngineDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, EngineDevelopmentHistory::query()->get());

    $historyRecord = EngineDevelopmentHistory::query()->first();

    $this->assertEquals($engine->id, $historyRecord->engine_season_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_ENGINE_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_ENGINE_DEV, $historyRecord->development);

    $this->assertEquals(BASE_ENGINE_RATING + BASE_ENGINE_DEV, $engine->refresh()->getComponentRating($component));
})->with('engine components');

it('updates existing engine development history records', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([
        $component => 60,
    ]);

    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($engine)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_ENGINE_RATING,
            'development' => HALF_ENGINE_DEV,
        ]);

    $engine->update([
        $component => BASE_ENGINE_RATING + HALF_ENGINE_DEV,
    ]);

    $entities = [generateDevelopmentEntity($engine, $component, BASE_ENGINE_DEV)];

    // Adds another BASE_ENGINE_DEV points to the component rating
    (new StoreEngineDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, EngineDevelopmentHistory::query()->get());

    $historyRecord = EngineDevelopmentHistory::query()->first();

    $this->assertEquals($engine->id, $historyRecord->engine_season_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_ENGINE_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_ENGINE_DEV + HALF_ENGINE_DEV, $historyRecord->development);

    $this->assertEquals(
        BASE_ENGINE_RATING + BASE_ENGINE_DEV + HALF_ENGINE_DEV,
        $engine->refresh()->getComponentRating($component),
    );
})->with('engine components');

it('does not record development history for engines that do not exist', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([
        $component => BASE_ENGINE_RATING,
    ]);

    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($engine)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_ENGINE_RATING,
            'development' => HALF_ENGINE_DEV,
        ]);

    // use the same engine as base, but replace its ID with a non-existent one
    $fake = generateDevelopmentEntity($engine, $component, BASE_ENGINE_DEV);
    $fake['id'] = '134';

    $entities = [
        generateDevelopmentEntity($engine, $component, BASE_ENGINE_DEV),
        $fake,
    ];

    (new StoreEngineDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, EngineDevelopmentHistory::query()->get());
    $this->assertEquals($engine->id, EngineDevelopmentHistory::query()->first()->engine_season_id);
})->with('engine components');

it('creates new records for new races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $engine = EngineSeason::factory()->for($season)->create([
        $component => 60,
    ]);

    // Creates dev for the first race, putting the component rating at BASE_ENGINE_RATING + HALF_ENGINE_DEV
    EngineDevelopmentHistory::factory()
        ->for($season)
        ->for($engine)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_ENGINE_RATING,
            'development' => HALF_ENGINE_DEV,
        ]);

    $engine->update([
        $component => BASE_ENGINE_RATING + HALF_ENGINE_DEV,
    ]);

    $engine->refresh();

    $race = Race::factory()->for($season)->create();

    // Adds BASE_ENGINE_DEV to the already updating component rating from last dev round
    $entities = [generateDevelopmentEntity($engine, $component, BASE_ENGINE_DEV)];

    (new StoreEngineDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(2, EngineDevelopmentHistory::query()->get());

    $historyRecord = EngineDevelopmentHistory::query()->latest('id')->first();

    $this->assertEquals($race->id, $historyRecord->race_id);
    // Initial rating is set after the initial dev round
    $this->assertEquals(BASE_ENGINE_RATING + HALF_ENGINE_DEV, $historyRecord->initial);
    $this->assertEquals(BASE_ENGINE_DEV, $historyRecord->development);

    // New rating is the value from the first race development round, plus the second race development round
    $this->assertEquals(
        BASE_ENGINE_RATING + BASE_ENGINE_DEV + HALF_ENGINE_DEV,
        $engine->refresh()->getComponentRating($component),
    );
})->with('engine components');
