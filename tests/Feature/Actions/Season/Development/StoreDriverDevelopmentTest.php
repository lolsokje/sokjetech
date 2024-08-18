<?php

use App\Actions\Season\Development\StoreDriverDevelopment;
use App\Models\DriverDevelopmentHistory;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

const BASE_DRIVER_RATING = 60;
const BASE_DRIVER_DEV = 10;
const HALF_DRIVER_DEV = BASE_DRIVER_DEV / 2;

it('creates new driver development history records when no history exists', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create([
        $component => BASE_DRIVER_RATING,
    ]);

    $entities = [generateDevelopmentEntity($racer, $component, BASE_DRIVER_DEV)];

    // Updates the rating to BASE_DRIVER_RATING + BASE_DRIVER_DEV
    (new StoreDriverDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, DriverDevelopmentHistory::query()->get());

    $historyRecord = DriverDevelopmentHistory::query()->first();

    $this->assertEquals($racer->id, $historyRecord->racer_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_DRIVER_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_DRIVER_DEV, $historyRecord->development);

    $this->assertEquals(BASE_DRIVER_RATING + BASE_DRIVER_DEV, $racer->refresh()->getComponentRating($component));
})->with('driver components');

it('updates existing driver development history records', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create([
        $component => BASE_DRIVER_RATING,
    ]);

    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($racer)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_DRIVER_RATING,
            'development' => HALF_DRIVER_DEV,
        ]);

    $racer->update([
        $component => BASE_DRIVER_RATING + HALF_DRIVER_DEV,
    ]);

    $entities = [generateDevelopmentEntity($racer, $component, BASE_DRIVER_DEV)];

    // Adds another BASE_DRIVER_DEV points to the component rating
    (new StoreDriverDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, DriverDevelopmentHistory::query()->get());

    $historyRecord = DriverDevelopmentHistory::query()->first();

    $this->assertEquals($racer->id, $historyRecord->racer_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_DRIVER_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_DRIVER_DEV + HALF_DRIVER_DEV, $historyRecord->development);

    $this->assertEquals(
        BASE_DRIVER_RATING + BASE_DRIVER_DEV + HALF_DRIVER_DEV,
        $racer->refresh()->getComponentRating($component),
    );
})->with('driver components');

it('does not record development history for drivers that do not exist', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create([
        $component => BASE_DRIVER_RATING,
    ]);

    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($racer)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_DRIVER_RATING,
            'development' => HALF_DRIVER_DEV,
        ]);

    // use the same driver as base, but replace its ID with a non-existent one
    $fake = generateDevelopmentEntity($racer, $component, BASE_DRIVER_DEV);
    $fake['id'] = '1234';

    $entities = [
        generateDevelopmentEntity($racer, $component, BASE_DRIVER_DEV),
        $fake,
    ];

    (new StoreDriverDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, DriverDevelopmentHistory::query()->get());
    $this->assertEquals($racer->id, DriverdevelopmentHistory::query()->first()->racer_id);
})->with('driver components');

it('creates new records for new races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create([
        $component => BASE_DRIVER_RATING,
    ]);

    // Creates dev for the first race, putting the component rating at BASE_DRIVER_RATING + HALF_DRIVER_DEV
    DriverDevelopmentHistory::factory()
        ->for($season)
        ->for($racer)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_DRIVER_RATING,
            'development' => HALF_DRIVER_DEV,
        ]);

    $racer->update([
        $component => BASE_DRIVER_RATING + HALF_DRIVER_DEV,
    ]);

    $racer->refresh();

    $race = Race::factory()->for($season)->create();

    // Adds BASE_DRIVER_DEV to the already updating component rating from last dev round
    $entities = [generateDevelopmentEntity($racer, $component, BASE_DRIVER_DEV)];

    (new StoreDriverDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(2, DriverDevelopmentHistory::query()->get());

    $historyRecord = DriverDevelopmentHistory::query()->latest('id')->first();

    $this->assertEquals($race->id, $historyRecord->race_id);
    // Initial rating is set after the initial dev round
    $this->assertEquals(BASE_DRIVER_RATING + HALF_DRIVER_DEV, $historyRecord->initial);
    $this->assertEquals(BASE_DRIVER_DEV, $historyRecord->development);

    // New rating is the value from the first race development round, plus the second race development round
    $this->assertEquals(
        BASE_DRIVER_RATING + BASE_DRIVER_DEV + HALF_DRIVER_DEV,
        $racer->refresh()->getComponentRating($component),
    );
})->with('driver components');
