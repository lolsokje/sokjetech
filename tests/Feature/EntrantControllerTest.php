<?php

use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can create entrants', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $team = Team::factory()->for($season->universe)->create();

    $this->actingAs($user)
        ->post(route('seasons.entrants.store', [$season]), getTeamCreationData($team))
        ->assertRedirect(route('seasons.entrants.index', [$season]));

    $this->assertDatabaseCount('entrants', 1);
    $this->assertCount(1, $season->entrants);
});

test('an unauthenticated user can\'t create entrants', function () {
    $season = Season::factory()->create();
    $team = Team::factory()->for($season->universe)->create();

    $this->post(route('seasons.entrants.store', [$season]), getTeamCreationData($team))
        ->assertForbidden();
});

test('an authenticated user can\'t create entrants in another user\'s universe', function () {
    $season = Season::factory()->create();
    $team = Team::factory()->for($season->universe)->create();
    $user = User::factory()->create();

    $this->post(route('seasons.entrants.store', [$season]), getTeamCreationData($team))
        ->assertForbidden();
});

test('a universe owner can update their entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->put(
            route('seasons.entrants.update', [$season, $entrant]),
            getTeamCreationData($entrant->team, ['full_name' => 'New full name']),
        )
        ->assertRedirect(route('seasons.entrants.index', [$season]));

    $this->assertEquals('New full name', $entrant->fresh()->full_name);
});

test('an unauthenticated user cant update entrants', function () {
    $entrant = Entrant::factory()->create();
    $fullName = $entrant->full_name;

    $this
        ->put(
            route('seasons.entrants.update', [$entrant->season, $entrant]),
            getTeamCreationData($entrant->team, ['full_name' => 'New full name']),
        )
        ->assertForbidden();

    $this->assertEquals($fullName, $entrant->fresh()->full_name);
});

test('an authenticated user cant update other users entrants', function () {
    $entrant = Entrant::factory()->create();
    $fullName = $entrant->full_name;

    $this->actingAs(User::factory()->create())
        ->put(
            route('seasons.entrants.update', [$entrant->season, $entrant]),
            getTeamCreationData($entrant->team, ['full_name' => 'New full name']),
        )
        ->assertForbidden();

    $this->assertEquals($fullName, $entrant->fresh()->full_name);
});

test('a universe owner can view the entrant create page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $season = Season::factory()->for($series)->create();
    Team::factory(2)->for($series->universe)->create();

    $this->actingAs($user)
        ->get(route('seasons.entrants.create', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Entrants/Create')
                ->has('teams', 2),
        );
});

test('an unauthenticated user cant view the entrant create page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.entrants.create', [$season]))
        ->assertRedirect(route('index'));
});

test('an authenticated user cant view another users entrant create page', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    $this->actingAs($user)
        ->get(route('seasons.entrants.create', [$season]))
        ->assertForbidden();
});

test('a universe owner can view the entrant edit page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Team::factory(2)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create(['team_id' => $season->teams()->first()->id]);

    $this->actingAs($user)
        ->get(route('seasons.entrants.edit', [$season, $entrant]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Entrants/Edit')
                ->has('teams', 2)
                ->where('entrant.full_name', $entrant->full_name),
        );
});

test('an unauthenticated user cant view the entrant edit page', function () {
    $entrant = Entrant::factory()->create();

    $this->get(route('seasons.entrants.edit', [$entrant->season, $entrant]))
        ->assertRedirect(route('index'));
});

test('an authenticated user cant view another users entrant edit page', function () {
    $entrant = Entrant::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.entrants.edit', [$entrant->season, $entrant]))
        ->assertForbidden();
});

it('shows all entrants for the selected season on the index page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(3)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.entrants.index', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Entrants/Index')
                ->has('season.entrants', 3),
        );
});

test('unauthorized users cannot delete a team', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), fn (Season $season) => $season->update(['started' => true]));
    Entrant::factory(3)->for($season)->create();

    $this->assertCount(3, $season->fresh()->entrants);

    $this->delete(route('seasons.entrants.destroy', [$season, Entrant::first()]))
        ->assertRedirect(route('index'));

    $this->actingAs(User::factory()->create())
        ->delete(route('seasons.entrants.destroy', [$season, Entrant::first()]))
        ->assertRedirect(route('index'));

    $this->assertCount(3, $season->fresh()->entrants);
});

test('a universe owner cannot delete a team when a season has been started', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), fn (Season $season) => $season->update(['started' => true]));
    Entrant::factory(3)->for($season)->create();

    $this->actingAs($user)
        ->delete(route('seasons.entrants.destroy', [$season, Entrant::first()]))
        ->assertRedirect();

    $this->assertCount(3, $season->fresh()->entrants);
});

test('a universe owner can delete an entrant before the season has started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(3)->for($season)->create();

    $this->actingAs($user)
        ->delete(route('seasons.entrants.destroy', [$season, Entrant::first()]))
        ->assertRedirect(route('seasons.entrants.index', [$season]));

    $this->assertCount(2, $season->fresh()->entrants);
});

it('deletes racers from a deleted entrant', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(3)->for($season)->create();

    foreach ($season->entrants as $entrant) {
        Racer::factory(2)->for($entrant)->create();
    }

    $this->assertDatabaseCount('racers', 6);

    $this->actingAs($user)
        ->delete(route('seasons.entrants.destroy', [$season, Entrant::first()]))
        ->assertRedirect(route('seasons.entrants.index', [$season]));

    $this->assertCount(2, $season->fresh()->entrants);
    $this->assertDatabaseCount('racers', 4);
});

function getTeamCreationData(Team $team, ?array $merge = []): array
{
    return array_merge([
        'team_id' => $team->id,
        'engine_id' => EngineSeason::factory()->create()->id,
        'full_name' => $team->full_name,
        'short_name' => $team->short_name,
        'team_principal' => $team->team_principal,
        'primary_colour' => $team->primary_colour,
        'secondary_colour' => $team->secondary_colour,
        'country' => $team->country,
    ], $merge);
}
