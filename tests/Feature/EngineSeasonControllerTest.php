<?php

use App\Models\Engine;
use App\Models\EngineSeason;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

test('a universe owner can add engines to a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $engine = Engine::factory()->for($season->series)->create();

    actingAs($user)
        ->post(route('seasons.engines.store', [$season]), [
            'base_engine_id' => $engine->id,
            'name' => $engine->name,
        ])
        ->assertRedirect(route('seasons.engines.index', [$season]));

    assertDatabaseCount('engine_seasons', 1);
    assertCount(1, $season->engines);
});

test('unauthorized users cannot add engines to seasons', function () {
    $season = Season::factory()->create();
    $engine = Engine::factory()->for($season->series)->create();

    post(route('seasons.engines.store', [$season]), [
        'base_engine_id' => $engine->id,
        'name' => $engine->name,
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
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

    actingAs($user)
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'rebadge' => true,
            'name' => 'Test',
        ])
        ->assertRedirect(route('seasons.engines.index', [$season]));

    assertEquals(true, $engine->fresh()->rebadge);
    assertEquals('Test', $engine->fresh()->name);
});

test('an authenticated user cannot add engines to another users season', function () {
    $season = Season::factory()->create();
    $engine = Engine::factory()->for($season->series)->create();

    actingAs(User::factory()->create())
        ->post(route('seasons.engines.store', [$season]), [
            'base_engine_id' => $engine->id,
            'name' => $engine->name
        ])
        ->assertForbidden();

    assertDatabaseCount('engine_seasons', 0);
    assertCount(0, $season->engines);
});

test('unauthorized users cannot update a seasons engines', function () {
    $season = Season::factory()->create();
    $engine = EngineSeason::factory()->for($season)->create();
    $name = $engine->name;

    put(route('seasons.engines.update', [$season, $engine]), [
        'base_engine_id' => $engine->baseEngine->id,
        'rebadge' => true,
        'name' => 'Test'
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'rebadge' => true,
            'name' => 'Test'
        ])
        ->assertForbidden();

    assertEquals(false, $engine->fresh()->rebadge);
    assertEquals($name, $engine->fresh()->name);
});

test('an authenticated user cannot update another users season engines', function () {
    $season = Season::factory()->create();
    $engine = EngineSeason::factory()->for($season)->create();
    $name = $engine->name;

    actingAs(User::factory()->create())
        ->put(route('seasons.engines.update', [$season, $engine]), [
            'base_engine_id' => $engine->baseEngine->id,
            'name' => 'New',
        ])
        ->assertForbidden();

    assertEquals($name, $engine->fresh()->name);
});

test('a season owner can view the add engine to season page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->get(route('seasons.engines.index', [$season]))
        ->assertOk();
});

test('unauthorized users cannot view the add engine to season page', function () {
    $season = Season::factory()->create();

    get(route('seasons.engines.create', [$season]))
        ->assertRedirect(route('index'));

    actingAs(User::factory()->create())
        ->get(route('seasons.engines.create', [$season]))
        ->assertForbidden();
});

test('a universe owner can view the update season engine page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $engine = EngineSeason::factory()->for($season)->create();

    actingAs($user)
        ->get(route('seasons.engines.edit', [$season, $engine]))
        ->assertOk();
});

test('an unauthorized user cannot view the update season engine page', function () {
    $engine = EngineSeason::factory()->create();

    get(route('seasons.engines.edit', [$engine->season, $engine]))
        ->assertRedirect(route('index'));

    actingAs(User::factory()->create())
        ->get(route('seasons.engines.edit', [$engine->season, $engine]))
        ->assertForbidden();
});

it('shows all engines added to a season on the index page', function () {
    $season = Season::factory()->create();
    EngineSeason::factory(3)->for($season)->create();

    get(route('seasons.engines.index', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Seasons/Engines/Index')
                ->has('engines', 3)
        );
});
