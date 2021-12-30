<?php

namespace Tests\Feature;

use App\Models\Circuit;
use App\Models\Race;
use App\Models\Season;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\TestCase;

class RaceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function aUniverseOwnerCanCreateRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);

        $this->actingAs($user)
            ->post(route('seasons.races.store', [$season]), $this->getRaceCreationData($season, $user))
            ->assertRedirect(route('seasons.races.index', [$season]));

        $this->assertDatabaseCount('races', 1);
        $this->assertCount(1, $season->races);
    }

    /** @test */
    public function anUnauthenticatedUserCannotCreateRaces()
    {
        $season = Season::factory()->create();

        $this->post(route('seasons.races.store', [$season]), $this->getRaceCreationData($season))
            ->assertForbidden();

        $this->assertDatabaseCount('races', 0);
        $this->assertCount(0, $season->races);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateRacesInAnotherUsersUniverse()
    {
        $user = User::factory()->create();
        $season = Season::factory()->create();

        $this->actingAs($user)
            ->post(route('seasons.races.store', [$season]), $this->getRaceCreationData($season))
            ->assertForbidden();

        $this->assertDatabaseCount('races', 0);
        $this->assertCount(0, $season->races);
    }

    /** @test */
    public function aUniverseOwnerCanUpdateRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        $race = Race::factory()->for($season)->create();

        $this->actingAs($user)
            ->put(route('seasons.races.update', [$season, $race]), [
                'circuit_id' => $race->circuit->id,
                'name' => 'New name',
                'stints' => $race->stints,
                'order' => $race->order,
            ])
            ->assertRedirect(route('seasons.races.index', [$season]));

        $this->assertEquals('New name', $race->fresh()->name);
    }

    /** @test */
    public function anUnauthenticatedUserCannotUpdateRaces()
    {
        $race = Race::factory()->create();
        $name = $race->name;

        $this->put(route('seasons.races.update', [$race->season, $race]), [
            'circuit_id' => $race->circuit->id,
            'name' => 'New name',
            'stints' => $race->stints,
            'order' => $race->order,
        ])
            ->assertForbidden();

        $this->assertEquals($name, $race->fresh()->name);
    }

    /** @test */
    public function anAuthenticatedUserCannotUpdateAnotherUsersRace()
    {
        $user = User::factory()->create();
        $race = Race::factory()->create();
        $name = $race->name;

        $this->actingAs($user)
            ->put(route('seasons.races.update', [$race->season, $race]), [
                'circuit_id' => $race->circuit->id,
                'name' => 'New name',
                'stints' => $race->stints,
                'order' => $race->order,
            ])
            ->assertForbidden();

        $this->assertEquals($name, $race->fresh()->name);
    }

    /** @test */
    public function newRacesAreAddedAfterAlreadyExistingRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        Race::factory()->for($season)->create(['order' => 1]);

        $this->actingAs($user)
            ->post(route('seasons.races.store', [$season]), [
                'circuit_id' => Circuit::factory()->create()->id,
                'name' => 'Test race with automatically generated order',
                'stints' => [['min_rng' => 0, 'max_rng' => 30]],
            ]);

        $race = $season->races()->where('order', 2)->first();
        $this->assertEquals('Test race with automatically generated order', $race->name);
    }

    /** @test */
    public function newRacesAreAddedAfterReorderedRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        $race1 = Race::factory()->for($season)->create(['order' => 1]);
        $race2 = Race::factory()->for($season)->create(['order' => 2]);

        $race1->update(['order' => 2]);
        $race2->update(['order' => 1]);

        $this->actingAs($user)
            ->post(route('seasons.races.store', [$season]), [
                'circuit_id' => Circuit::factory()->create()->id,
                'name' => 'Test race with automatically generated order',
                'stints' => [['min_rng' => 0, 'max_rng' => 30]],
            ]);

        $race = $season->races()->where('order', 3)->first();
        $this->assertEquals('Test race with automatically generated order', $race->name);
    }

    /** @test */
    public function stintMinRngMustBeLowerThanMaxRng()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);

        $circuit = Circuit::factory()->for($user)->create();

        $this->actingAs($user)
            ->post(route('seasons.races.store', [$season]), [
                'circuit_id' => $circuit->id,
                'name' => 'Test race with higher min RNG than max RNG',
                'stints' => [
                    ['min_rng' => 0, 'max_rng' => 30],
                    ['min_rng' => 30, 'max_rng' => 0],
                ],
            ])
            ->assertSessionHasErrors(['stints' => 'Min stint RNG must be lower than max stint RNG']);

        $this->post(route('seasons.races.store', [$season]), [
            'circuit_id' => $circuit->id,
            'name' => 'Test race with equal min and max RNG',
            'stints' => [
                ['min_rng' => 0, 'max_rng' => 30],
                ['min_rng' => 30, 'max_rng' => 30],
            ],
        ])
            ->assertSessionHasErrors(['stints' => 'Min stint RNG must be lower than max stint RNG']);

        $this->post(route('seasons.races.store', [$season]), [
            'circuit_id' => $circuit->id,
            'name' => 'Test race with correct min and max RNG',
            'stints' => [
                ['min_rng' => 0, 'max_rng' => 30],
                ['min_rng' => 0, 'max_rng' => 30],
            ],
        ])
            ->assertSessionDoesntHaveErrors(['stints']);
    }

    /** @test */
    public function aUniverseOwnerCanViewRaceCreatePage()
    {
        $universe = Universe::factory()->create();
        $season = $this->createSeasonForUser($universe->user);

        $this->actingAs($universe->user)
            ->get(route('seasons.races.create', $season))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewRaceCreatePage()
    {
        $season = Season::factory()->create();

        $this->get(route('seasons.races.create', [$season]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewCreateRacePageInOtherUniverses()
    {
        $season = Season::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('seasons.races.create', [$season]))
            ->assertForbidden();
    }

    /** @test */
    public function aUniverseOwnerCanViewRaceEditPage()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        $race = Race::factory()->for($season)->create();

        $this->actingAs($user)
            ->get(route('seasons.races.edit', [$season, $race]))
            ->assertOk();
    }

    /** @test */
    public function aUniverseOwnerCanViewRaceReorderPage()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        Race::factory(5)->for($season)->create();

        $this->actingAs($user)
            ->get(route('seasons.races.reorder', [$season]))
            ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->component('Races/Reorder')
                ->has('season.races', 5)
            );
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewRaceReorderPage()
    {
        $race = Race::factory()->create();

        $this->get(route('seasons.races.reorder', [$race->season]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewRaceReorderRacesPageForOtherUniverse()
    {
        $race = Race::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('seasons.races.reorder', [$race->season]))
            ->assertForbidden();
    }

    /** @test */
    public function aUniverseOwnerCanReorderRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        $raceOne = Race::factory()->for($season)->create(['order' => 1]);
        $raceTwo = Race::factory()->for($season)->create(['order' => 2]);

        $this->actingAs($user)
            ->put(route('seasons.races.order', [$season]), [
                'races' => [
                    ['id' => $raceOne->id, 'order' => 2],
                    ['id' => $raceTwo->id, 'order' => 1],
                ],
            ])
            ->assertRedirect(route('seasons.races.index', [$season]));

        $this->assertEquals(2, $raceOne->fresh()->order);
        $this->assertEquals(1, $raceTwo->fresh()->order);
    }

    /** @test */
    public function theIndexPageShowsAllRaces()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        Race::factory(5)->for($season)->create();

        $this->actingAs($user)
            ->get(route('seasons.races.index', [$season]))
            ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->component('Races/Index')
                ->has('season.races', 5)
            );
    }

    /** @test */
    public function theShowPageShowsTheCorrectRace()
    {
        $user = User::factory()->create();
        $season = $this->createSeasonForUser($user);
        Race::factory(5)->for($season)->create();

        $race = $season->races()->first();

        $this->actingAs($user)
            ->get(route('seasons.races.show', [$season, $race]))
            ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->where('season.year', $season->year)
                ->where('race.name', $race->name)
            );
    }

    private function getRaceCreationData(Season $season, ?User $user = null): array
    {
        if (!$user) {
            $user = User::factory()->create();
        }

        return [
            'circuit_id' => Circuit::factory()->for($user)->create()->id,
            'name' => "$season->year Test Grand Prix",
            'stints' => [['min_rng' => 0, 'max_rng' => 30]],
            'order' => 1,
        ];
    }

    private function createSeasonForUser(User $user): Season
    {
        $series = $this->createSeriesForUser($user);

        return Season::factory()->for($series)->create();
    }
}
