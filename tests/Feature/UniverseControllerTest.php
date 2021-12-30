<?php

use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\Assert;

test('an autheorized user can create universes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('universes.store'), [
            'name' => 'Universe name',
            'visibility' => Universe::VISIBILITY_PUBLIC,
        ])
        ->assertRedirect(route('universes.index'));

    $this->assertDatabaseCount('universes', 1);
    $this->assertCount(1, $user->universes);
});

test('an unauthorized user can\'t create universes', function () {
    $this->post(route('universes.store'), [
        'name' => 'Universe name',
        'visibility' => Universe::VISIBILITY_PUBLIC,
    ])
        ->assertForbidden();
    $this->assertDatabaseCount('universes', 0);
});

test('an unauthorized user can\'t view the create universe page', function () {
    $this->get(route('universes.create'))
        ->assertRedirect(route('index'));
});

test('an authorized user can update their universes', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('universes.update', [$universe]), [
            'name' => 'New name',
            'visibility' => Universe::VISIBILITY_PRIVATE,
        ])
        ->assertRedirect(route('universes.index'));

    $this->assertEquals('New name', $universe->fresh()->name);
    $this->assertEquals(Universe::VISIBILITY_PRIVATE, $universe->fresh()->visibility);
});

test('an authorized user can\'t update someone else\'s universes', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $universe = Universe::factory()->create(['user_id' => $firstUser->id]);
    $name = $universe->name;
    $visibility = $universe->visibility;

    $this->actingAs($secondUser)
        ->put(route('universes.update', [$universe]), [
            'name' => 'New name',
            'visibility' => Universe::VISIBILITY_PRIVATE,
        ])
        ->assertForbidden();

    $this->assertEquals($name, $universe->fresh()->name);
    $this->assertEquals($visibility, $universe->fresh()->visibility);
});

test('an authenticated user can view the universe create page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('universes.create'))
        ->assertOk();
});

test('an authenticated user can view the universe edit page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('universes.edit', [$universe]))
        ->assertOk();
});

test('an unauthorized user can\'t view the edit universe page', function () {
    $this->get(route('universes.edit', Universe::factory()->create()))
        ->assertRedirect(route('index'));
});

test('an authorized user can\'t view another user\'s edit universe page', function () {
    $universe = Universe::factory()->create(['user_id' => User::factory()->create()->id]);

    $this->actingAs(User::factory()->create())
        ->get(route('universes.edit', [$universe]))
        ->assertForbidden();
});

test('an unauthorized user can\'t update universes', function () {
    $universe = Universe::factory()->create();
    $name = $universe->name;
    $visibility = $universe->visibility;

    $this->put(route('universes.update', [$universe]), [
        'name' => 'New name',
        'visibility' => Universe::VISIBILITY_PRIVATE,
    ])
        ->assertForbidden();

    $this->assertEquals($name, $universe->fresh()->name);
    $this->assertEquals($visibility, $universe->fresh()->visibility);
});

it('only shows public and their own universes to authenticated users', function () {
    $user = User::factory()->create();

    Universe::factory()->create(['visibility' => Universe::VISIBILITY_PRIVATE]);
    Universe::factory()->create(['visibility' => Universe::VISIBILITY_PUBLIC]);
    Universe::factory()->create(['visibility' => Universe::VISIBILITY_AUTH]);

    $this->actingAs($user);

    $this->get(route('universes.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->has('universes', 2)
        );
});

it('only shows public universes to unauthenticated users', function () {
    Universe::factory()->create(['visibility' => Universe::VISIBILITY_PRIVATE]);
    Universe::factory()->create(['visibility' => Universe::VISIBILITY_PUBLIC]);
    Universe::factory()->create(['visibility' => Universe::VISIBILITY_AUTH]);

    $this->get(route('universes.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->has('universes', 1));
});
