<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function aUniverseOwnerCanCreateSeasons()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);

        $this->actingAs($user)
            ->post(route('series.seasons.store', [$series]), [
                'year' => 2021,
            ])
            ->assertRedirect(route('series.seasons.index', [$series]));

        $this->assertDatabaseCount('seasons', 1);
        $this->assertCount(1, $series->seasons);
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreateSeasons()
    {
        $series = Series::factory()->create();

        $this->post(route('series.seasons.store', [$series]), [
            'year' => 2021,
        ])
            ->assertForbidden();

        $this->assertDatabaseCount('seasons', 0);
        $this->assertCount(0, $series->seasons);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateSeasonsInAnotherUsersUniverse()
    {
        $user = User::factory()->create();
        $series = Series::factory()->create();

        $this->actingAs($user)
            ->post(route('series.seasons.store', [$series]), [
                'year' => 2021,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('seasons', 0);
        $this->assertCount(0, $series->seasons);
    }

    /** @test */
    public function aUniverseOwnerCanUpdateTheirSeasons()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        $season = Season::factory()->for($series)->create();

        $this->actingAs($user)
            ->put(route('series.seasons.update', [$series, $season]), [
                'year' => 2022,
            ])
            ->assertRedirect(route('series.seasons.index', [$series]));

        $this->assertEquals(2022, $season->fresh()->year);
    }

    /** @test */
    public function anUnauthenticatedUserCannotUpdateSeasons()
    {
        $season = Season::factory()->create();
        $year = $season->year;

        $this->put(route('series.seasons.update', [$season->series, $season]), [
            'year' => 2022,
        ])
            ->assertForbidden();

        $this->assertEquals($year, $season->fresh()->year);
    }

    /** @test */
    public function anAuthenticatedUserCannotUpdateAnotherUsersSeasons()
    {
        $user = User::factory()->create();
        $season = Season::factory()->create();
        $year = $season->year;

        $this->actingAs($user)
            ->put(route('series.seasons.update', [$season->series, $season]), [
                'year' => 2022,
            ])
            ->assertForbidden();

        $this->assertEquals($year, $season->fresh()->year);
    }

    /** @test */
    public function aUniverseOwnerCanViewTheSeasonCreatePage()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);

        $this->actingAs($user)
            ->get(route('series.seasons.create', [$series]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheSeasonCreatePage()
    {
        $series = Series::factory()->create();

        $this->get(route('series.seasons.create', [$series]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersSeasonCreatePage()
    {
        $user = User::factory()->create();
        $series = Series::factory()->create();

        $this->actingAs($user)
            ->get(route('series.seasons.create', [$series]))
            ->assertForbidden();
    }

    /** @test */
    public function aUniverseOwnerCanViewTheSeasonEditPage()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        $season = Season::factory()->for($series)->create();

        $this->actingAs($user)
            ->get(route('series.seasons.edit', [$series, $season]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheSeasonEditPage()
    {
        $series = Series::factory()->create();
        $season = Season::factory()->for($series)->create();

        $this->get(route('series.seasons.edit', [$series, $season]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersSeasonEditPage()
    {
        $user = User::factory()->create();
        $series = Series::factory()->create();
        $season = Season::factory()->for($series)->create();

        $this->actingAs($user)
            ->get(route('series.seasons.edit', [$series, $season]))
            ->assertForbidden();
    }

    /** @test */
    public function aYearHasToBeUniqueWithinASeries()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        Season::factory()->for($series)->create(['year' => 2021]);

        $this->actingAs($user)
            ->post(route('series.seasons.store', [$series]), [
                'year' => 2021
            ])
            ->assertSessionHasErrors(['year' => 'The year must be unique in this series']);
    }

    /** @test */
    public function aSeasonCanBeUpdatedWhileRetainingTheSameYear()
    {
        $user = User::factory()->create();
        $series = $this->createSeriesForUser($user);
        $season = Season::factory()->for($series)->create(['year' => 2021]);

        $this->actingAs($user)
            ->put(route('series.seasons.update', [$series, $season]), [
                'year' => 2021
            ])
            ->assertRedirect(route('series.seasons.index', [$series]));
    }

    private function createSeriesForUser(User $user): Series
    {
        return Series::factory()->for(Universe::factory()->for($user)->create())->create();
    }
}
