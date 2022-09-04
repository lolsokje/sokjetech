<?php


use App\Models\Driver;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;

test('authenticated users can share drivers with others', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('universes.drivers.store', $universe), getDriver(['shared' => true]))
        ->assertRedirect(route('universes.drivers.index', [$universe]));

    $this->assertTrue(Driver::first()->shared);
});

test('an authenticated user can unshare drivers', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    $driver = Driver::factory()->for($universe)->shared()->create();

    $this->actingAs($user)
        ->put(route('universes.drivers.update', [$universe, $driver]), getDriver(['shared' => false]))
        ->assertRedirect(route('universes.drivers.index', $universe));

    assertFalse(Driver::first()->shared);
});

test('unauthenticated users cannot view the drivers database index page', function () {
    $this->get(route('database.drivers.index'))
        ->assertRedirect(route('index'));
});

it('only shows shared drivers on the driver database index page', function () {
    Driver::factory(10)->sequence(
        ['shared' => false],
        ['shared' => true],
    )->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.drivers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Drivers/Index')
            ->has('drivers', 5),
        );
});

it('groups drivers by full_name', function () {
    $user = User::factory()->create();
    Driver::factory(2)->shared()->create([
        'first_name' => 'Test short name',
        'last_name' => 'Test full name',
    ]);
    Driver::factory()->shared()->create();

    $this->actingAs($user)
        ->get(route('database.drivers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Drivers/Index')
            ->has('drivers', 2));
});

test('an authenticated user can copy a driver to a specific universe', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->post(route('database.drivers.copy', $driver), [
            'universe_id' => $universe->id,
        ])
        ->assertRedirect(route('database.drivers.index'));

    assertDatabaseCount('drivers', 2);
    assertCount(1, $universe->drivers()->get());
});

test('a destination universe is required and must exist', function () {
    $user = User::factory()->create();
    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->post(route('database.drivers.copy', $driver))
        ->assertInvalid(['universe_id' => 'required']);

    assertDatabaseCount('drivers', 1);

    $this->actingAs($user)
        ->post(route('database.drivers.copy', $driver), [
            'universe_id' => 'fake',
        ])
        ->assertInvalid(['universe_id' => 'The selected universe id is invalid.']);
});

test('the destination universe must be owned by the logged in user', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create();
    $driver = Driver::factory()->create();

    $this->actingAs($user)
        ->post(route('database.drivers.copy', $driver), [
            'universe_id' => $universe->id,
        ])
        ->assertForbidden();

    assertDatabaseCount('drivers', 1);
    assertCount(0, $universe->drivers()->get());
});

test('an unauthenticated user cannot copy drivers', function () {
    $universe = Universe::factory()->create();
    $driver = Driver::factory()->create();

    $this->post(route('database.drivers.copy', $driver), [
        'universe_id' => $universe->id,
    ])
        ->assertRedirect(route('index'));

    assertDatabaseCount('drivers', 1);
    assertCount(0, $universe->drivers()->get());
});

it('paginates shared drivers', function () {
    Driver::factory(40)->shared()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.drivers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Drivers/Index')
            ->has('drivers', 20)
            ->has('links', 4));
});

it('shows the universes owned by the authenticated user on the index page', function () {
    $user = User::factory()->create();
    Universe::factory(3)->for($user)->create();
    Universe::factory()->create();

    $this->actingAs($user)
        ->get(route('database.drivers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Drivers/Index')
            ->has('universes', 3));
});

function getDriver(?array $merge = []): array
{
    return array_merge(
        [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => '1970-01-01',
            'country' => 'NL',
            'shared' => false,
        ],
        $merge,
    );
}
