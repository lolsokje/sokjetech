<?php

use App\Actions\Season\Development\StoreTeamDevelopment;
use App\Models\Entrant;
use App\Models\Race;
use App\Models\Season;
use App\Models\TeamDevelopmentHistory;
use App\ValueObjects\Season\Development\DevelopmentEntity;

const BASE_TEAM_RATING = 40;
const BASE_TEAM_DEV = 8;
const HALF_TEAM_DEV = BASE_TEAM_DEV / 2;

it('creates new team development history records when no history exists', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $team = Entrant::factory()->for($season)->create([
        $component => BASE_TEAM_RATING,
    ]);

    $entities = [generateDevelopmentEntity($team, $component, BASE_TEAM_DEV)];

    // Updates the rating to BASE_TEAM_RATING + BASE_TEAM_DEV
    (new StoreTeamDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, TeamDevelopmentHistory::query()->get());

    $historyRecord = TeamDevelopmentHistory::query()->first();

    $this->assertEquals($team->id, $historyRecord->entrant_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_TEAM_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_TEAM_DEV, $historyRecord->development);

    $this->assertEquals(BASE_TEAM_RATING + BASE_TEAM_DEV, $team->refresh()->getComponentRating($component));
})->with('team components');

it('updates existing team development history records', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $team = Entrant::factory()->for($season)->create([
        $component => 60,
    ]);

    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($team)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_TEAM_RATING,
            'development' => HALF_TEAM_DEV,
        ]);

    $team->update([
        $component => BASE_TEAM_RATING + HALF_TEAM_DEV,
    ]);

    $entities = [generateDevelopmentEntity($team, $component, BASE_TEAM_DEV)];

    // Adds another BASE_TEAM_DEV points to the component rating
    (new StoreTeamDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, TeamDevelopmentHistory::query()->get());

    $historyRecord = TeamDevelopmentHistory::query()->first();

    $this->assertEquals($team->id, $historyRecord->entrant_id);
    $this->assertEquals($race->id, $historyRecord->race_id);
    $this->assertEquals(BASE_TEAM_RATING, $historyRecord->initial);
    $this->assertEquals(BASE_TEAM_DEV + HALF_TEAM_DEV, $historyRecord->development);

    $this->assertEquals(
        BASE_TEAM_RATING + BASE_TEAM_DEV + HALF_TEAM_DEV,
        $team->refresh()->getComponentRating($component),
    );
})->with('team components');

it('does not record development history for teams that do not exist', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $team = Entrant::factory()->for($season)->create([
        $component => BASE_TEAM_RATING,
    ]);

    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($team)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_TEAM_RATING,
            'development' => HALF_TEAM_DEV,
        ]);

    // use the same team as base, but replace its ID with a non-existent one
    $fake = generateDevelopmentEntity($team, $component, BASE_TEAM_DEV);
    $fake['id'] = '134';

    $entities = [
        generateDevelopmentEntity($team, $component, BASE_TEAM_DEV),
        $fake,
    ];

    (new StoreTeamDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(1, TeamDevelopmentHistory::query()->get());
    $this->assertEquals($team->id, TeamDevelopmentHistory::query()->first()->entrant_id);
})->with('team components');

it('creates new records for new races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->create();
    $team = Entrant::factory()->for($season)->create([
        $component => 60,
    ]);

    // Creates dev for the first race, putting the component rating at BASE_TEAM_RATING + HALF_TEAM_DEV
    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($team)
        ->for($race)
        ->create([
            'component' => $component,
            'initial' => BASE_TEAM_RATING,
            'development' => HALF_TEAM_DEV,
        ]);

    $team->update([
        $component => BASE_TEAM_RATING + HALF_TEAM_DEV,
    ]);

    $team->refresh();

    $race = Race::factory()->for($season)->create();

    // Adds BASE_TEAM_DEV to the already updating component rating from last dev round
    $entities = [generateDevelopmentEntity($team, $component, BASE_TEAM_DEV)];

    (new StoreTeamDevelopment)->handle(
        seasonId: $season->id,
        raceId: $race->id,
        entities: DevelopmentEntity::fromRequest($entities),
        component: $component,
    );

    $this->assertCount(2, TeamDevelopmentHistory::query()->get());

    $historyRecord = TeamDevelopmentHistory::query()->latest('id')->first();

    $this->assertEquals($race->id, $historyRecord->race_id);
    // Initial rating is set after the initial dev round
    $this->assertEquals(BASE_TEAM_RATING + HALF_TEAM_DEV, $historyRecord->initial);
    $this->assertEquals(BASE_TEAM_DEV, $historyRecord->development);

    // New rating is the value from the first race development round, plus the second race development round
    $this->assertEquals(
        BASE_TEAM_RATING + BASE_TEAM_DEV + HALF_TEAM_DEV,
        $team->refresh()->getComponentRating($component),
    );
})->with('team components');
