<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function aUniverseOwnerCanCreateTeams()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('universes.teams.store', [$universe]), [
            'full_name' => 'Scuderia Ferrari',
            'short_name' => 'Ferrari',
            'team_principal' => 'Mattio Binotto',
            'primary_colour' => '#FF0000',
            'secondary_colour' => '#FFFFFF',
            'country' => 'IT',
        ]);

        $response->assertRedirect(route('universes.teams.index', [$universe]));

        $this->assertDatabaseCount('teams', 1);
        $this->assertCount(1, $universe->teams);
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreateTeams()
    {
        $universe = Universe::factory()->create();

        $response = $this->post(route('universes.teams.store', [$universe]), [
            'full_name' => 'Scuderia Ferrari',
            'short_name' => 'Ferrari',
            'team_principal' => 'Mattio Binotto',
            'primary_colour' => '#FF0000',
            'secondary_colour' => '#FFFFFF',
            'country' => 'IT',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('teams', 0);
        $this->assertCount(0, $universe->teams);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateTeamsInAnotherUsersUniverse()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('universes.teams.store', [$universe]), [
            'full_name' => 'Scuderia Ferrari',
            'short_name' => 'Ferrari',
            'team_principal' => 'Mattio Binotto',
            'primary_colour' => '#FF0000',
            'secondary_colour' => '#FFFFFF',
            'country' => 'IT',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('teams', 0);
        $this->assertCount(0, $universe->teams);
    }

    /** @test */
    public function aUniverseOwnerCanUpdateTheirTeams()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);
        $team = Team::factory()->create(['universe_id' => $universe->id]);

        $this->actingAs($user);

        $response = $this->put(route('universes.teams.update', [$universe, $team]), [
            'full_name' => 'New Full Name',
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'country' => $team->country,
        ]);

        $response->assertRedirect(route('universes.teams.index', [$universe]));

        $this->assertEquals('New Full Name', $team->fresh()->full_name);
    }

    /** @test */
    public function anUnauthenticatedUserCannotUpdateTeams()
    {
        $universe = Universe::factory()->create();
        $team = Team::factory()->create(['universe_id' => $universe->id]);
        $fullName = $team->full_name;

        $response = $this->put(route('universes.teams.update', [$universe, $team]), [
            'full_name' => 'New Full Name',
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'country' => $team->country,
        ]);

        $response->assertForbidden();

        $this->assertEquals($fullName, $team->fresh()->full_name);
    }

    /** @test */
    public function anAuthenticatedUserCannotUpdateAnotherUsersTeams()
    {
        $user = User::factory()->create();

        $universe = Universe::factory()->create();
        $team = Team::factory()->create(['universe_id' => $universe->id]);
        $fullName = $team->full_name;

        $this->actingAs($user);

        $response = $this->put(route('universes.teams.update', [$universe, $team]), [
            'full_name' => 'New Full Name',
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'country' => $team->country,
        ]);

        $response->assertForbidden();

        $this->assertEquals($fullName, $team->fresh()->full_name);
    }

    /** @test */
    public function aUniverseOwnerCanViewTeamCreatePage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('universes.teams.create', [$universe]))
            ->assertOk();
    }

    /** @test */
    public function aUniverseOwnerCanViewTeamEditPage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->for($user)->create();
        $team = Team::factory()->for($universe)->create();

        $this->actingAs($user)
            ->get(route('universes.teams.edit', [$universe, $team]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTeamCreatePage()
    {
        $universe = Universe::factory()->create();

        $response = $this->get(route('universes.teams.create', [$universe]));

        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTeamEditPage()
    {
        $universe = Universe::factory()->create();
        $team = Team::factory()->create(['universe_id' => $universe->id]);

        $response = $this->get(route('universes.teams.edit', [$universe, $team]));

        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersTeamCreatePage()
    {
        $universe = Universe::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('universes.teams.create', [$universe]));

        $response->assertForbidden();
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersTeamEditPage()
    {
        $universe = Universe::factory()->create();
        $team = Team::factory()->create(['universe_id' => $universe->id]);

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('universes.teams.edit', [$universe, $team]));

        $response->assertForbidden();
    }

    /** @test */
    public function theShowPageShowsTheCorrectTeam()
    {
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
    }

    /** @test */
    public function theIndexPageShowsAllTeamsInSelectedUniverse()
    {
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
    }
}
