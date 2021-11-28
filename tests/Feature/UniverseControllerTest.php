<?php

namespace Tests\Feature;

use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniverseControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anAuthorizedUserCanCreateUniverses()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('universes.store'), [
            'name' => 'Universe name',
            'visibility' => Universe::VISIBILITY_PUBLIC,
        ]);

        $response->assertRedirect(route('universes.index'));

        $this->assertDatabaseCount('universes', 1);
        $this->assertCount(1, $user->universes);
    }

    /** @test */
    public function anUnauthorizedUserCannotCreateUniverses()
    {
        $response = $this->post(route('universes.store'), [
            'name' => 'Universe name',
            'visibility' => Universe::VISIBILITY_PUBLIC,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('universes', 0);
    }

    /** @test */
    public function anAuthorizedUserCanUpdateTheirUniverses()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->put(route('universes.update', [$universe]), [
            'name' => 'New name',
            'visibility' => Universe::VISIBILITY_PRIVATE,
        ]);

        $response->assertRedirect(route('universes.index'));

        $this->assertEquals('New name', $universe->fresh()->name);
        $this->assertEquals(Universe::VISIBILITY_PRIVATE, $universe->fresh()->visibility);
    }

    /** @test */
    public function anAuthorizedUserCannotUpdateSomeoneElsesUniverses()
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $universe = Universe::factory()->create(['user_id' => $firstUser->id]);
        $name = $universe->name;
        $visibility = $universe->visibility;

        $this->actingAs($secondUser);

        $response = $this->put(route('universes.update', [$universe]), [
            'name' => 'New name',
            'visibility' => Universe::VISIBILITY_PRIVATE,
        ]);

        $response->assertForbidden();

        $this->assertEquals($name, $universe->fresh()->name);
        $this->assertEquals($visibility, $universe->fresh()->visibility);
    }

    /** @test */
    public function anUnauthorizedUserCannotUpdateUniverses()
    {
        $universe = Universe::factory()->create();
        $name = $universe->name;
        $visibility = $universe->visibility;

        $response = $this->put(route('universes.update', [$universe]), [
            'name' => 'New name',
            'visibility' => Universe::VISIBILITY_PRIVATE,
        ]);

        $response->assertForbidden();

        $this->assertEquals($name, $universe->fresh()->name);
        $this->assertEquals($visibility, $universe->fresh()->visibility);
    }
}
