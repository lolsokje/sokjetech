<?php

use App\Models\Engine;
use App\Models\EngineSeason;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can add engines to a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $engine = Engine::factory()->for($season->series)->create();

    $this->actingAs($user)
        ->post(route('seasons.engines.store', [$season]), [
            'base_engine_id' => $engine->id,
            'name' => $engine->name,
        ])
        ->assertRedirect(route('seasons.engines.index', [$season]));

    $this->assertDatabaseCount('engine_seasons', 1);
    $this->assertCount(1, $season->engines);
});

test('unauthorized users cannot add engines to seasons', function () {
    $season = Season::factory()->create();
    $engine = Engine::factory()->for($season->series)->create();

    $this->post(route('seasons.engines.store', [$season]), [
        'base_engine_id' => $engine->id,
        'name' => $engine->name,
    ])
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.engines.store', [$season]), [
            'base_engine_id' => $engine->id,
            'name' => $engine->name,
        ])
        ->assertForbidden();
});

test('a universe owner can update a seasons engines', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $engine = EngineSeason::factory()->for($season)->create();

    $this->actingAs($user)
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'rebadge' => true,
            'name' => 'Test',
        ])
        ->assertRedirect(route('seasons.engines.index', [$season]));

    $this->assertEquals(true, $engine->fresh()->rebadge);
    $this->assertEquals('Test', $engine->fresh()->name);
});

test('an authenticated user cannot add engines to another users season', function () {
    $season = Season::factory()->create();
    $engine = Engine::factory()->for($season->series)->create();

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.engines.store', [$season]), [
            'base_engine_id' => $engine->id,
            'name' => $engine->name,
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('engine_seasons', 0);
    $this->assertCount(0, $season->engines);
});

test('unauthorized users cannot update a seasons engines', function () {
    $season = Season::factory()->create();
    $engine = EngineSeason::factory()->for($season)->create();
    $name = $engine->name;

    $this->put(route('seasons.engines.update', [$season, $engine]), [
        'base_engine_id' => $engine->baseEngine->id,
        'rebadge' => true,
        'name' => 'Test',
    ])
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'rebadge' => true,
            'name' => 'Test',
        ])
        ->assertForbidden();

    $this->assertEquals(false, $engine->fresh()->rebadge);
    $this->assertEquals($name, $engine->fresh()->name);
});

test('an authenticated user cannot update another users season engines', function () {
    $season = Season::factory()->create();
    $engine = EngineSeason::factory()->for($season)->create();
    $name = $engine->name;

    $this->actingAs(User::factory()->create())
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'name' => 'New',
        ])
        ->assertForbidden();

    $this->assertEquals($name, $engine->fresh()->name);
});

test('a season owner can view the add engine to season page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Engine::factory(3)->for($season->series)->create();

    $this->actingAs($user)
        ->get(route('seasons.engines.create', [$season]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Seasons/Engines/Create')
            ->has('engines', 3));
});

test('unauthorized users cannot view the add engine to season page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.engines.create', [$season]))
        ->assertRedirect(route('index'));

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.engines.create', [$season]))
        ->assertForbidden();
});

test('a universe owner can view the update season engine page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $baseEngine = Engine::factory()->for($season->series)->create();
    $engine = EngineSeason::factory()->for($season)->create(['base_engine_id' => $baseEngine->id]);

    $this->actingAs($user)
        ->get(route('seasons.engines.edit', [$season, $engine]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Seasons/Engines/Edit')
            ->has('engines', 1));
});

test('an unauthorized user cannot view the update season engine page', function () {
    $engine = EngineSeason::factory()->create();

    $this->get(route('seasons.engines.edit', [$engine->season, $engine]))
        ->assertRedirect(route('index'));

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.engines.edit', [$engine->season, $engine]))
        ->assertForbidden();
});

it('shows all engines added to a season on the index page', function () {
    $season = Season::factory()->create();
    EngineSeason::factory(3)->for($season)->create();

    $this->get(route('seasons.engines.index', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Seasons/Engines/Index')
                ->has('engines', 3),
        );
});
