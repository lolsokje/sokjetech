<?php

namespace Tests\Feature;

use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\TestCase;

class UniverseControllerTest extends TestCase
{
    /** @test */
    public function anAuthorizedUserCanCreateUniverses()
    {
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
    public function anUnauthorizedUserCannotViewTheCreateUniversePage()
    {
        $this->get(route('universes.create'))
            ->assertRedirect(route('index'));
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
    public function anAuthenticatedUserCanViewUniverseCreatePage()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('universes.create'))
            ->assertOk();
    }

    /** @test */
    public function anAuthenticatedUserCanViewUniverseEditPage()
    {
        $user = User::factory()->create();
        $universe = Universe::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('universes.edit', [$universe]))
            ->assertOk();
    }

    /** @test */
    public function anUnauthorizedUserCannotViewTheEditUniversePage()
    {
        $this->get(route('universes.edit', Universe::factory()->create()))
            ->assertRedirect(route('index'));
    }

    /** @test */
    public function anAuthorizedUserCannotViewAnotherUsersEditUniversePage()
    {
        $universe = Universe::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs(User::factory()->create())
            ->get(route('universes.edit', [$universe]))
            ->assertForbidden();
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

    /** @test */
    public function anAuthorizedUserCanOnlyViewPublicAndTheirOwnUniverses()
    {
        $user = User::factory()->create();

        Universe::factory()->create(['visibility' => Universe::VISIBILITY_PRIVATE]);
        Universe::factory()->create(['visibility' => Universe::VISIBILITY_PUBLIC]);
        Universe::factory()->create(['visibility' => Universe::VISIBILITY_AUTH]);

        $this->actingAs($user);

        $response = $this->get(route('universes.index'));

        $response
            ->assertInertia(fn(Assert $page) => $page
                ->has('universes', 2)
            );
    }

    /** @test */
    public function anUnauthorizedUserCanOnlyViewPublicUniverses()
    {
        Universe::factory()->create(['visibility' => Universe::VISIBILITY_PRIVATE]);
        Universe::factory()->create(['visibility' => Universe::VISIBILITY_PUBLIC]);
        Universe::factory()->create(['visibility' => Universe::VISIBILITY_AUTH]);

        $this->get(route('universes.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->has('universes', 1));
    }
}
