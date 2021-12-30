<?php

namespace Tests\Feature;

use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\TestCase;

class SeriesControllerTest extends TestCase
{
    /** @test */
    public function aUniverseOwnerCanCreateSeriesInTheirUniverse()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('universes.series.store', [$universe]), [
            'name' => 'Formula One',
        ]);

        $response->assertRedirect(route('universes.series.index', [$universe]));

        $this->assertDatabaseCount('series', 1);
        $this->assertCount(1, $universe->series);
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreateASeries()
    {
        $universe = Universe::factory()->create();

        $response = $this->post(route('universes.series.store', [$universe]), [
            'name' => 'Formula One',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('series', 0);
        $this->assertCount(0, $universe->series);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateSeriesInAnotherUsersUniverse()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('universes.series.store', [$universe]), [
            'name' => 'Formula One',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('series', 0);
        $this->assertCount(0, $universe->series);
    }

    /** @test */
    public function aUniverseOwnerCanUpdateTheirSeries()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);
        $series = Series::factory()->create(['universe_id' => $universe->id]);

        $this->actingAs($user);

        $response = $this->put(route('universes.series.update', [$universe, $series]), [
            'name' => 'Formula One',
        ]);

        $response->assertRedirect(route('universes.series.index', [$universe]));

        $this->assertEquals('Formula One', $series->fresh()->name);
    }

    /** @test */
    public function anUnauthenticatedUserCannotEditSeries()
    {
        $universe = Universe::factory()->create();
        $series = Series::factory()->create(['universe_id' => $universe->id]);
        $name = $series->name;

        $response = $this->put(route('universes.series.update', [$universe, $series]), [
            'name' => 'Formula One',
        ]);

        $response->assertForbidden();

        $this->assertEquals($name, $series->fresh()->name);
    }

    /** @test */
    public function aUserCannotEditAnotherUsersSeries()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create();
        $series = Series::factory()->create(['universe_id' => $universe->id]);
        $name = $series->name;

        $this->actingAs($user);

        $response = $this->put(route('universes.series.update', [$universe, $series]), [
            'name' => 'Formula One',
        ]);

        $response->assertForbidden();

        $this->assertEquals($name, $series->fresh()->name);
    }

    /** @test */
    public function aUniverseOwnerCanSeeViewSeriesCreatePage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('universes.series.create', [$universe]))
            ->assertOk();
    }

    /** @test */
    public function aUniverseOwnerCanViewSeriesEditPage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->for($user)->create();
        $series = Series::factory()->for($universe)->create();

        $this->actingAs($user)
            ->get(route('universes.series.edit', [$universe, $series]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheSeriesCreatePage()
    {
        $universe = Universe::factory()->create();

        $response = $this->get(route('universes.series.create', [$universe]));

        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheSeriesEditPage()
    {
        $universe = Universe::factory()->create();
        $series = Series::factory()->create(['universe_id' => $universe->id]);

        $response = $this->get(route('universes.series.edit', [$universe, $series]));

        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function aUserCannotViewAnotherUsersSeriesCreatePage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('universes.series.create', [$universe]));

        $response->assertForbidden();
    }

    /** @test */
    public function aUserCannotViewAnotherUsersSeriesEditPage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create();
        $series = Series::factory()->create(['universe_id' => $universe->id]);

        $this->actingAs($user);

        $response = $this->get(route('universes.series.edit', [$universe, $series]));

        $response->assertForbidden();
    }

    /** @test */
    public function theIndexPageShowsAllSeriesInTheSelectedUniverse()
    {
        $universe = Universe::factory()->create();
        Series::factory(5)->for($universe)->create();
        Series::factory(5)->create();

        $this->actingAs($universe->user)
            ->get(route('universes.series.index', [$universe]))
            ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->component('Series/Index')
                ->has('universe.series', 5)
            );
    }
}
