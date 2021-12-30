<?php

namespace Tests\Feature;

use App\Models\Engine;
use App\Models\Series;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\TestCase;

class EngineControllerTest extends TestCase
{
    /** @test */
    public function aUniverseOwnerCanCreateEngines()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);

        $this->actingAs($user)
            ->post(route('series.engines.store', [$series]), [
                'name' => 'Honda',
            ])
            ->assertRedirect(route('series.engines.index', [$series]));

        $this->assertDatabaseCount('engines', 1);
        $this->assertCount(1, $series->engines);
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreateEngines()
    {
        $series = Series::factory()->create();

        $this->post(route('series.engines.store', [$series]), [
            'name' => 'Honda',
        ])
            ->assertForbidden();

        $this->assertDatabaseCount('engines', 0);
        $this->assertCount(0, $series->engines);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateEnginesInAnotherUsersUniverse()
    {
        $user = User::factory()->create();
        $series = Series::factory()->create();

        $this->actingAs($user)
            ->post(route('series.engines.store', [$series]), [
                'name' => 'Honda',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('engines', 0);
        $this->assertCount(0, $series->engines);
    }

    /** @test */
    public function aUniverseOwnerCanEditTheirEngines()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        $engine = Engine::factory()->for($series)->create();

        $this->actingAs($user)
            ->put(route('series.engines.update', [$series, $engine]), [
                'name' => 'Ferrari',
            ])
            ->assertRedirect(route('series.engines.index', [$series]));

        $this->assertEquals('Ferrari', $engine->fresh()->name);
    }

    /** @test */
    public function anUnauthenticatedUserCannotUpdateEngines()
    {
        $series = Series::factory()->create();
        $engine = Engine::factory()->for($series)->create();
        $name = $engine->name;

        $this->put(route('series.engines.update', [$series, $engine]), [
            'name' => 'Ferrari',
        ])
            ->assertForbidden();

        $this->assertEquals($name, $engine->fresh()->name);
    }

    /** @test */
    public function anAuthenticatedUserCannotUpdateAnotherUsersEngines()
    {
        $user = User::factory()->create();
        $engine = Engine::factory()->create();
        $name = $engine->name;

        $this->actingAs($user)
            ->put(route('series.engines.update', [$engine->series, $engine]), [
                'name' => 'Ferrari',
            ])
            ->assertForbidden();

        $this->assertEquals($name, $engine->fresh()->name);
    }

    /** @test */
    public function aUniverseOwnerCanViewTheCreateEnginePage()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);

        $this->actingAs($user)
            ->get(route('series.engines.create', [$series]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheCreateEnginePage()
    {
        $series = Series::factory()->create();

        $this->get(route('series.engines.create', [$series]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersCreateEnginePage()
    {
        $user = User::factory()->create();
        $series = Series::factory()->create();

        $this->actingAs($user)
            ->get(route('series.engines.create', [$series]))
            ->assertForbidden();
    }

    /** @test */
    public function aUniverseOwnerCanViewTheUpdateEnginePage()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        $engine = Engine::factory()->for($series)->create();

        $this->actingAs($user)
            ->get(route('series.engines.edit', [$series, $engine]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheUpdateEnginePage()
    {
        $engine = Engine::factory()->create();

        $this->get(route('series.engines.edit', [$engine->series, $engine]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersUpdateEnginePage()
    {
        $user = User::factory()->create();
        $engine = Engine::factory()->create();

        $this->actingAs($user)
            ->get(route('series.engines.edit', [$engine->series, $engine]))
            ->assertForbidden();
    }

    /** @test */
    public function theIndexPageShowsAllEnginesInTheSelectedSeries()
    {
        $series = Series::factory()->create();
        Engine::factory(3)->for($series)->create();
        Engine::factory(3)->for(Series::factory()->create());

        $this->actingAs($series->user)
            ->get(route('series.engines.index', [$series]))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Engines/Index')
                ->has('series.engines', 3)
            );
    }
}
