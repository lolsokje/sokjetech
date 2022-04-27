<?php

use App\Models\Team;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can create teams', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('universes.teams.store', [$universe]), [
            'full_name' => 'Scuderia Ferrari',
            'short_name' => 'Ferrari',
            'team_principal' => 'Mattio Binotto',
            'primary_colour' => '#FF0000',
            'secondary_colour' => '#FFFFFF',
            'country' => 'IT',
        ])
        ->assertRedirect(route('universes.teams.index', [$universe]));

    $this->assertDatabaseCount('teams', 1);
    $this->assertCount(1, $universe->teams);
});

test('an unauthenticated user can\'t create teams', function () {
    $universe = Universe::factory()->create();

    $this->post(route('universes.teams.store', [$universe]), [
        'full_name' => 'Scuderia Ferrari',
        'short_name' => 'Ferrari',
        'team_principal' => 'Mattio Binotto',
        'primary_colour' => '#FF0000',
        'secondary_colour' => '#FFFFFF',
        'country' => 'IT',
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('teams', 0);
    $this->assertCount(0, $universe->teams);
});

test('an authenticated user can\'t create teams in another user\'s universe', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();

    $this->actingAs($user)
        ->post(route('universes.teams.store', [$universe]), [
            'full_name' => 'Scuderia Ferrari',
            'short_name' => 'Ferrari',
            'team_principal' => 'Mattio Binotto',
            'primary_colour' => '#FF0000',
            'secondary_colour' => '#FFFFFF',
            'country' => 'IT',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('teams', 0);
    $this->assertCount(0, $universe->teams);
});

test('a universe owner can update their teams', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);
    $team = Team::factory()->create(['universe_id' => $universe->id]);

    $this->actingAs($user)
        ->put(route('universes.teams.update', [$universe, $team]), [
            'full_name' => 'New Full Name',
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'country' => $team->country,
        ])
        ->assertRedirect(route('universes.teams.index', [$universe]));

    $this->assertEquals('New Full Name', $team->fresh()->full_name);
});

test('an unauthenticated user can\'t update teams', function () {
    $universe = Universe::factory()->create();
    $team = Team::factory()->create(['universe_id' => $universe->id]);
    $fullName = $team->full_name;

    $this->put(route('universes.teams.update', [$universe, $team]), [
        'full_name' => 'New Full Name',
        'short_name' => $team->short_name,
        'team_principal' => $team->team_principal,
        'primary_colour' => $team->primary_colour,
        'secondary_colour' => $team->secondary_colour,
        'country' => $team->country,
    ])
        ->assertForbidden();

    $this->assertEquals($fullName, $team->fresh()->full_name);
});

test('an authenticatead user can\'t update another user\'s teams', function () {
    $user = User::factory()->create();

    $universe = Universe::factory()->create();
    $team = Team::factory()->create(['universe_id' => $universe->id]);
    $fullName = $team->full_name;

    $this->actingAs($user)
        ->put(route('universes.teams.update', [$universe, $team]), [
            'full_name' => 'New Full Name',
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'country' => $team->country,
        ])
        ->assertForbidden();

    $this->assertEquals($fullName, $team->fresh()->full_name);
});

test('a universe owner can view the team create page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('universes.teams.create', [$universe]))
        ->assertOk();
});

test('a universe owner can view the team edit page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    $team = Team::factory()->for($universe)->create();

    $this->actingAs($user)
        ->get(route('universes.teams.edit', [$universe, $team]))
        ->assertOk();
});

test('an unauthenticated user can\'t view the team create page', function () {
    $universe = Universe::factory()->create();

    $this->get(route('universes.teams.create', [$universe]))
        ->assertRedirect(route('index'));
});

test('an unauthenticated user can\'t view the team edit page', function () {
    $universe = Universe::factory()->create();
    $team = Team::factory()->create(['universe_id' => $universe->id]);

    $this->get(route('universes.teams.edit', [$universe, $team]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s team create page', function () {
    $universe = Universe::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('universes.teams.create', [$universe]))
        ->assertForbidden();
});

test('an authenticated user can\'t view another user\'s team edit page', function () {
    $universe = Universe::factory()->create();
    $team = Team::factory()->create(['universe_id' => $universe->id]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('universes.teams.edit', [$universe, $team]))
        ->assertForbidden();
});

it('shows the correct team on the show page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    Team::factory(5)->for($universe)->create();
    $team = $universe->teams()->first();

    $this->actingAs($user)
        ->get(route('universes.teams.show', [$universe, $team]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Teams/View')
            ->where('team.full_name', $team->full_name)
        );
});

it('shows all teams in the selected universe on the index page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    Team::factory(5)->for($universe)->create();
    Team::factory(5)->create();

    $this->actingAs($user)
        ->get(route('universes.teams.index', [$universe]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Teams/Index')
            ->has('universe.teams', 5)
        );
});
