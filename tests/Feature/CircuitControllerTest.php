<?php

namespace Tests\Feature;

use App\Models\Circuit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\TestCase;

class CircuitControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anAuthorizedUserCanCreateACircuit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('circuits.store'), [
            'name' => 'Zandvoort',
            'country' => 'nl'
        ]);

        $response->assertRedirect(route('circuits.index'));

        $this->assertDatabaseCount('circuits', 1);
        $this->assertCount(1, $user->circuits);
    }

    /** @test */
    public function anUnauthorizedUserCannotCreateACircuit()
    {
        $response = $this->post(route('circuits.store'), [
            'name' => 'Zandvoort',
            'country' => 'nl'
        ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('circuits', 0);
    }

    /** @test */
    public function anAuthorizedUserCanUpdateTheirOwnCircuits()
    {
        $user = User::factory()->create();
        $circuit = Circuit::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->put(route('circuits.update', [$circuit]), [
            'name' => 'New name',
            'country' => 'NC'
        ]);

        $response->assertRedirect(route('circuits.index'));

        $this->assertEquals('New name', $circuit->fresh()->name);
        $this->assertEquals('NC', $circuit->fresh()->country);
    }

    /** @test */
    public function anAuthorizedUserCannotUpdateSomeoneElsesCircuits()
    {
        $user = User::factory()->create();
        $circuit = Circuit::factory()->create(['user_id' => $user->id]);
        $name = $circuit->name;
        $country = $circuit->country;

        $this->actingAs(User::factory()->create());

        $response = $this->put(route('circuits.update', [$circuit]), [
            'name' => 'New name',
            'country' => 'NC'
        ]);

        $response->assertForbidden();

        $this->assertEquals($name, $circuit->fresh()->name);
        $this->assertEquals($country, $circuit->fresh()->country);
    }

    /** @test */
    public function anAuthenticatedUserCanViewTheCircuitCreatePage()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('circuits.create'))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheCircuitCreatePage()
    {
        $this->get(route('circuits.create'))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCanViewTheCircuitEditPage()
    {
        $user = User::factory()->create();
        $circuit = Circuit::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('circuits.edit', [$circuit]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthenticatedUserCannotViewTheCircuitEditPage()
    {
        $circuit = Circuit::factory()->create();

        $this->get(route('circuits.edit', [$circuit]))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthenticatedUserCannotViewAnotherUsersCircuitEditPage()
    {
        $circuit = Circuit::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('circuits.edit', [$circuit]))
            ->assertForbidden();
    }

    /** @test */
    public function theIndexPageShowsAllCircuitsCreatedByAUser()
    {
        $user = User::factory()->create();
        Circuit::factory(5)->for($user)->create();

        $this->actingAs($user)
            ->get(route('circuits.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Circuits/Index')
                ->has('circuits.data', 5) // circuits.data since circuits are paginated
                ->has('filters')
            );
    }
}
