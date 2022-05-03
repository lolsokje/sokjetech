<?php

use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can create series in their universe', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('universes.series.store', [$universe]), [
            'name' => 'Formula One',
        ])
        ->assertRedirect(route('universes.series.index', [$universe]));

    $this->assertDatabaseCount('series', 1);
    $this->assertCount(1, $universe->series);
});

test('an unauthenticated user can\'t create a series', function () {
    $universe = Universe::factory()->create();

    $this->post(route('universes.series.store', [$universe]), [
        'name' => 'Formula One',
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('series', 0);
    $this->assertCount(0, $universe->series);
});

test('an authenticated user can\'t create series in another user\'s universe', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();

    $this->actingAs($user)
        ->post(route('universes.series.store', [$universe]), [
            'name' => 'Formula One',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('series', 0);
    $this->assertCount(0, $universe->series);
});

test('a universe owner can update their series', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);
    $series = Series::factory()->create(['universe_id' => $universe->id]);

    $this->actingAs($user)
        ->put(route('universes.series.update', [$universe, $series]), [
            'name' => 'Formula One',
        ])
        ->assertRedirect(route('universes.series.index', [$universe]));

    $this->assertEquals('Formula One', $series->fresh()->name);
});

test('an authenticated user can\'t edit series', function () {
    $universe = Universe::factory()->create();
    $series = Series::factory()->create(['universe_id' => $universe->id]);
    $name = $series->name;

    $this->put(route('universes.series.update', [$universe, $series]), [
        'name' => 'Formula One',
    ])
        ->assertForbidden();

    $this->assertEquals($name, $series->fresh()->name);
});

test('a user can\'t edit another user\'s series', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();
    $series = Series::factory()->create(['universe_id' => $universe->id]);
    $name = $series->name;

    $this->actingAs($user)
        ->put(route('universes.series.update', [$universe, $series]), [
            'name' => 'Formula One',
        ])
        ->assertForbidden();

    $this->assertEquals($name, $series->fresh()->name);
});

test('a universe owner can view the series create page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('universes.series.create', [$universe]))
        ->assertOk();
});

test('a universe owner can view the series edit page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    $series = Series::factory()->for($universe)->create();

    $this->actingAs($user)
        ->get(route('universes.series.edit', [$universe, $series]))
        ->assertOk();
});

test('an unauthenticated user can\'t view the series create page', function () {
    $universe = Universe::factory()->create();

    $response = $this->get(route('universes.series.create', [$universe]));

    $response->assertRedirect(route('index'));
});

test('an unauthenticated user can\'t view the series edit page', function () {
    $universe = Universe::factory()->create();
    $series = Series::factory()->create(['universe_id' => $universe->id]);

    $this->get(route('universes.series.edit', [$universe, $series]))
        ->assertRedirect(route('index'));
});

test('a user can\'t view another user\'s series create page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();

    $this->actingAs($user)
        ->get(route('universes.series.create', [$universe]))
        ->assertForbidden();
});

test('a user can\'t view another user\'s series edit page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();
    $series = Series::factory()->create(['universe_id' => $universe->id]);

    $this->actingAs($user)
        ->get(route('universes.series.edit', [$universe, $series]))
        ->assertForbidden();
});

it('shows all series in the selected universe on the index page', function () {
    $universe = Universe::factory()->create();
    Series::factory(3)->for($universe)->create();
    Series::factory(3)->create();

    $this->actingAs($universe->user)
        ->get(route('universes.series.index', [$universe]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Series/Index')
                ->has('universe.series', 3)
        );
});
