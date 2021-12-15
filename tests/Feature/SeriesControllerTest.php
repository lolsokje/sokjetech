<?php

namespace Tests\Feature;

use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesControllerTest extends TestCase
{
    use RefreshDatabase;

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
}
