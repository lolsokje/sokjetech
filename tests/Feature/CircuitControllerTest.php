<?php

namespace Tests\Feature;

use App\Models\Circuit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
