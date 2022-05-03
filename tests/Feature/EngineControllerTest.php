<?php

use App\Models\Engine;
use App\Models\Series;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can create engines', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $this->actingAs($user)
        ->post(route('series.engines.store', [$series]), [
            'name' => 'Honda',
        ])
        ->assertRedirect(route('series.engines.index', [$series]));

    $this->assertDatabaseCount('engines', 1);
    $this->assertCount(1, $series->engines);
});

test('an unauthenticated user can\'t create engines', function () {
    $series = Series::factory()->create();

    $this->post(route('series.engines.store', [$series]), [
        'name' => 'Honda',
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('engines', 0);
    $this->assertCount(0, $series->engines);
});

test('an authenticated user can\'t create engines in another user\'s universe', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();

    $this->actingAs($user)
        ->post(route('series.engines.store', [$series]), [
            'name' => 'Honda',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('engines', 0);
    $this->assertCount(0, $series->engines);
});

test('a universe owner can edit their engines', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $engine = Engine::factory()->for($series)->create();

    $this->actingAs($user)
        ->put(route('series.engines.update', [$series, $engine]), [
            'name' => 'Ferrari',
        ])
        ->assertRedirect(route('series.engines.index', [$series]));

    $this->assertEquals('Ferrari', $engine->fresh()->name);
});

test('an unauthenticated user can\'t update engines', function () {
    $series = Series::factory()->create();
    $engine = Engine::factory()->for($series)->create();
    $name = $engine->name;

    $this->put(route('series.engines.update', [$series, $engine]), [
        'name' => 'Ferrari',
    ])
        ->assertForbidden();

    $this->assertEquals($name, $engine->fresh()->name);
});

test('an authenticated user can\'t update another user\'s engines', function () {
    $user = User::factory()->create();
    $engine = Engine::factory()->create();
    $name = $engine->name;

    $this->actingAs($user)
        ->put(route('series.engines.update', [$engine->series, $engine]), [
            'name' => 'Ferrari',
        ])
        ->assertForbidden();

    $this->assertEquals($name, $engine->fresh()->name);
});

test('a universe owner can view the create engine page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $this->actingAs($user)
        ->get(route('series.engines.create', [$series]))
        ->assertOk();
});

test('an authenticated user can\'t view the create engine page', function () {
    $series = Series::factory()->create();

    $this->get(route('series.engines.create', [$series]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s create engine page', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();

    $this->actingAs($user)
        ->get(route('series.engines.create', [$series]))
        ->assertForbidden();
});

test('a universe owner can view the update engine page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $engine = Engine::factory()->for($series)->create();

    $this->actingAs($user)
        ->get(route('series.engines.edit', [$series, $engine]))
        ->assertOk();
});

test('an authenticated user can\'t view the update engine page', function () {
    $engine = Engine::factory()->create();

    $this->get(route('series.engines.edit', [$engine->series, $engine]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s update engine page', function () {
    $user = User::factory()->create();
    $engine = Engine::factory()->create();

    $this->actingAs($user)
        ->get(route('series.engines.edit', [$engine->series, $engine]))
        ->assertForbidden();
});

it('shows all engines in the selected series on the index page', function () {
    $series = Series::factory()->create();
    Engine::factory(3)->for($series)->create();
    Engine::factory(3)->for(Series::factory()->create());

    $this->actingAs($series->user)
        ->get(route('series.engines.index', [$series]))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Engines/Index')
                ->has('series.engines', 3)
        );
});
