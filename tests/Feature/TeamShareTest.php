<?php

use App\Models\Team;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;

test('authenticated users can share teams with others', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('universes.teams.store', $universe), getTeam(['shared' => true]))
        ->assertRedirect(route('universes.teams.index', [$universe]));

    $this->assertTrue(Team::first()->shared);
});

test('an authenticated user can unshare teams', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    $team = Team::factory()->for($universe)->shared()->create();

    $this->actingAs($user)
        ->put(route('universes.teams.update', [$universe, $team]), getTeam(['shared' => false]))
        ->assertRedirect(route('universes.teams.index', $universe));

    assertFalse(Team::first()->shared);
});

test('unauthenticated users cannot view the teams database index page', function () {
    $this->get(route('database.teams.index'))
        ->assertRedirect(route('index'));
});

it('only shows shared teams on the team database index page', function () {
    Team::factory(10)->sequence(
        ['shared' => false],
        ['shared' => true],
    )->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.teams.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Teams/Index')
            ->has('teams', 5),
        );
});

it('groups teams by short name, full_name adn team_principal', function () {
    $user = User::factory()->create();
    Team::factory(2)->shared()->create([
        'short_name' => 'Test short name',
        'full_name' => 'Test full name',
        'team_principal' => 'Test team principal',
    ]);
    Team::factory()->shared()->create();

    $this->actingAs($user)
        ->get(route('database.teams.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Teams/Index')
            ->has('teams', 2));
});

test('an authenticated user can copy a team to a specific universe', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $team = Team::factory()->create();

    $this->actingAs($user)
        ->post(route('database.teams.copy', $team), [
            'universe_id' => $universe->id,
        ])
        ->assertRedirect(route('database.teams.index'));

    assertDatabaseCount('teams', 2);
    assertCount(1, $universe->teams()->get());
});

test('a destination universe is required and must exist', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($user)
        ->post(route('database.teams.copy', $team))
        ->assertInvalid(['universe_id' => 'required']);

    assertDatabaseCount('teams', 1);

    $this->actingAs($user)
        ->post(route('database.teams.copy', $team), [
            'universe_id' => 'fake',
        ])
        ->assertInvalid(['universe_id' => 'The selected universe id is invalid.']);
});

test('the destination universe must be owned by the logged in user', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($user)
        ->post(route('database.teams.copy', $team), [
            'universe_id' => $universe->id,
        ])
        ->assertForbidden();

    assertDatabaseCount('teams', 1);
    assertCount(0, $universe->teams()->get());
});

test('an unauthenticated user cannot copy teams', function () {
    $universe = Universe::factory()->create();
    $team = Team::factory()->create();

    $this->post(route('database.teams.copy', $team), [
        'universe_id' => $universe->id,
    ])
        ->assertRedirect(route('index'));

    assertDatabaseCount('teams', 1);
    assertCount(0, $universe->teams()->get());
});

it('paginates shared teams', function () {
    Team::factory(40)->shared()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.teams.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Teams/Index')
            ->has('teams', 20)
            ->has('links', 4));
});

it('shows the universes owned by the authenticated user on the index page', function () {
    $user = User::factory()->create();
    Universe::factory(3)->for($user)->create();
    Universe::factory()->create();

    $this->actingAs($user)
        ->get(route('database.teams.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Teams/Index')
            ->has('universes', 3));
});

function getTeam(?array $merge = []): array
{
    return array_merge(
        [
            'full_name' => 'Full name',
            'short_name' => 'Short name',
            'team_principal' => 'Team principal',
            'country' => 'NL',
            'primary_colour' => '#000000',
            'secondary_colour' => '#FFFFFF',
            'accent_colour' => '#000000',
            'shared' => false,
        ],
        $merge,
    );
}
