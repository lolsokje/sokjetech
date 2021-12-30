<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\TestCase;

class DriverControllerTest extends TestCase
{
    /** @test */
    public function aUniverseOwnerCanCreateDrivers()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post(route('universes.drivers.store', [$universe]), [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => null,
            'country' => 'AF',
        ]);

        $response->assertRedirect(route('universes.drivers.index', [$universe]));

        $this->assertDatabaseCount('drivers', 1);
        $this->assertCount(1, $universe->drivers);
    }

    /** @test */
    public function unauthenticatedUsersCannotCreateDrivers()
    {
        $universe = Universe::factory()->create();

        $response = $this->post(route('universes.drivers.store', [$universe]), [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => now()->format('Y-m-d'),
            'country' => 'AF',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('drivers', 0);
        $this->assertCount(0, $universe->drivers);
    }

    /** @test */
    public function anAuthenticatedUserCannotCreateDriversInOtherUniverses()
    {
        $universe = Universe::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('universes.drivers.store', [$universe]), [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => now()->format('Y-m-d'),
            'country' => 'AF',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('drivers', 0);
        $this->assertCount(0, $universe->drivers);
    }

    /** @test */
    public function aUniverseOwnerCanEditDrivers()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);
        $driver = Driver::factory()->create(['universe_id' => $universe->id]);

        $this->actingAs($user);

        $newDate = now()->subMonth()->format('Y-m-d');
        $response = $this->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => 'New First',
            'last_name' => 'New Last',
            'dob' => $newDate,
            'country' => 'AL',
        ]);

        $response->assertRedirect(route('universes.drivers.index', [$universe]));

        $this->assertEquals('New First', $driver->fresh()->first_name);
        $this->assertEquals('New Last', $driver->fresh()->last_name);
        $this->assertEquals($newDate, $driver->fresh()->dob->format('Y-m-d'));
        $this->assertEquals('AL', $driver->fresh()->country);
    }

    /** @test */
    public function anUnauthenticatedUserCannotUpdateDrivers()
    {
        $universe = Universe::factory()->create();
        $driver = Driver::factory()->create(['universe_id' => $universe->id]);

        $newDate = now()->subMonth()->format('Y-m-d');
        $response = $this->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => 'New First',
            'last_name' => 'New Last',
            'dob' => $newDate,
            'country' => 'AL',
        ]);

        $response->assertForbidden();

        $this->assertEquals($driver->first_name, $driver->fresh()->first_name);
        $this->assertEquals($driver->last_name, $driver->fresh()->last_name);
        $this->assertEquals($driver->dob, $driver->fresh()->dob);
        $this->assertEquals($driver->country, $driver->fresh()->country);
    }

    /** @test */
    public function anAuthenticatedUserCannotEditDriversInOtherUniverses()
    {
        $universe = Universe::factory()->create();
        $user = User::factory()->create();
        $driver = Driver::factory()->create(['universe_id' => $universe->id]);

        $this->actingAs($user);

        $newDate = now()->subMonth()->format('Y-m-d');
        $response = $this->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => 'New First',
            'last_name' => 'New Last',
            'dob' => $newDate,
            'country' => 'AL',
        ]);

        $response->assertForbidden();

        $this->assertEquals($driver->first_name, $driver->fresh()->first_name);
        $this->assertEquals($driver->last_name, $driver->fresh()->last_name);
        $this->assertEquals($driver->dob, $driver->fresh()->dob);
        $this->assertEquals($driver->country, $driver->fresh()->country);
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewDriverCreatePage()
    {
        $this->get(route('universes.drivers.create', [Universe::factory()->create()]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewDriverEditPage()
    {
        $universe = Universe::factory()->create();
        $driver = Driver::factory()->create(['universe_id' => $universe->id]);
        $this->get(route('universes.drivers.edit', [$universe, $driver]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCanViewDriverCreatePage()
    {
        $universe = Universe::factory()->create();

        $this->actingAs($universe->user)
            ->get(route('universes.drivers.create', [$universe]))
            ->assertOk();
    }

    /** @test */
    public function anAuthenticatedUserCanViewDriverEditPage()
    {
        $universe = Universe::factory()->create();
        $driver = Driver::factory()->for($universe)->create();

        $this->actingAs($universe->user)
            ->get(route('universes.drivers.edit', [$universe, $driver]))
            ->assertOk();
    }

    /** @test */
    public function theIndexPageShowsAllDriversInTheSelectedUniverse()
    {
        $universe = Universe::factory()->create();
        Driver::factory(5)->for($universe)->create();
        Driver::factory(5)->for(Universe::factory()->create())->create();

        $this->actingAs($universe->user)
            ->get(route('universes.drivers.index', [$universe]))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Drivers/Index')
                ->has('universe.drivers', 5)
            );
    }
}
