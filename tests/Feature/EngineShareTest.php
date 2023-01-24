<?php

use App\Models\Engine;
use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;

test('authenticated users can share engines with others', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $this->actingAs($user)
        ->post(route('series.engines.store', $series), [
            'name' => 'Engine',
            'shared' => true,
        ])
        ->assertRedirect(route('series.engines.index', [$series]));

    $this->assertTrue(Engine::first()->shared);
});

test('an authenticated user can unshare engines', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $engine = Engine::factory()->for($series)->shared()->create();

    $this->actingAs($user)
        ->put(route('series.engines.update', [$series, $engine]), [
            'name' => 'Name',
            'shared' => false,
        ])
        ->assertRedirect(route('series.engines.index', $series));

    assertFalse(Engine::first()->shared);
});

test('unauthenticated users cannot view the engines database index page', function () {
    $this->get(route('database.engines.index'))
        ->assertRedirect(route('index'));
});

it('only shows shared engines on the team database index page', function () {
    Engine::factory(10)->sequence(
        ['shared' => false],
        ['shared' => true],
    )->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.engines.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Engines/Index')
            ->has('engines', 5),
        );
});

it('groups engines by name', function () {
    $user = User::factory()->create();
    Engine::factory(2)->shared()->create(['name' => 'Engine']);
    Engine::factory()->shared()->create();

    $this->actingAs($user)
        ->get(route('database.engines.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Engines/Index')
            ->has('engines', 2));
});

test('an authenticated user can copy an engine to a specific series', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $engine = Engine::factory()->create();

    $this->actingAs($user)
        ->post(route('database.engines.copy', $engine), [
            'series_id' => $series->id,
        ])
        ->assertRedirect(route('database.engines.index'));

    assertDatabaseCount('engines', 2);
    assertCount(1, $series->engines()->get());
});

test('a destination series is required and must exist', function () {
    $user = User::factory()->create();
    $engine = Engine::factory()->create();

    $this->actingAs($user)
        ->post(route('database.engines.copy', $engine))
        ->assertInvalid(['series_id' => 'required']);

    assertDatabaseCount('engines', 1);

    $this->actingAs($user)
        ->post(route('database.engines.copy', $engine), [
            'series_id' => 'fake',
        ])
        ->assertInvalid(['series_id' => 'The selected series id is invalid.']);
});

test('the destination series must be owned by the logged in user', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();
    $engine = Engine::factory()->create();

    $this->actingAs($user)
        ->post(route('database.engines.copy', $engine), [
            'series_id' => $series->id,
        ])
        ->assertForbidden();

    assertDatabaseCount('engines', 1);
    assertCount(0, $series->engines()->get());
});

test('an unauthenticated user cannot copy engines', function () {
    $series = Universe::factory()->create();
    $engine = Engine::factory()->create();

    $this->post(route('database.engines.copy', $engine), [
        'series_id' => $series->id,
    ])
        ->assertRedirect(route('index'));

    assertDatabaseCount('engines', 1);
    assertCount(0, $series->engines()->get());
});

it('paginates shared engines', function () {
    Engine::factory(40)->shared()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.engines.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Engines/Index')
            ->has('engines', 20)
            ->has('links', 4));
});

it('shows the series owned by the authenticated user on the index page', function () {
    $user = User::factory()->create();
    createSeriesForUser($user);
    createSeriesForUser($user);
    createSeriesForUser($user);
    Series::factory()->create();

    $this->actingAs($user)
        ->get(route('database.engines.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Engines/Index')
            ->has('series', 3));
});
