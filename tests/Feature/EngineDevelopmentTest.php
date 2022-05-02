<?php

use App\Models\EngineSeason;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertEquals;

test('a universe owner can view the engine development page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    EngineSeason::factory(4)->for($season)->create();

    actingAs($user)
        ->get(route('seasons.development.engines', [$season]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Development/Engines')
            ->has('season')
            ->has('engines', 4)
        );
});

test('an unauthorized user cant view the engine development page', function () {
    $season = Season::factory()->create();
    EngineSeason::factory(4)->for($season)->create();

    get(route('seasons.development.engines', [$season]))
        ->assertForbidden();
});

test('an authorized user cannot view the engine development page for another users universe', function () {
    $season = Season::factory()->create();
    EngineSeason::factory(4)->for($season)->create();

    actingAs(User::factory()->create())
        ->get(route('seasons.development.engines', [$season]))
        ->assertForbidden();
});

test('a universe owner can update driver ratings', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    [$engineOne, $engineTwo] = EngineSeason::factory(2)->for($season)->create(['rating' => 10]);

    actingAs($user)
        ->post(route('seasons.development.engines.store', [$season]), [
            'engines' => [
                ['id' => $engineOne->id, 'new' => 12],
                ['id' => $engineTwo->id, 'new' => 15],
            ],
        ])
        ->assertRedirect(route('seasons.development.engines', [$season]));

    assertEquals(12, $engineOne->fresh()->rating);
    assertEquals(15, $engineTwo->fresh()->rating);
});

test('an unauthenticated user cannot update engine ratings', function () {
    $season = Season::factory()->create();
    [$engineOne, $engineTwo] = EngineSeason::factory(2)->for($season)->create(['rating' => 10]);

    $this->post(route('seasons.development.drivers.store', [$season]), [
        'engines' => [
            ['id' => $engineOne->id, 'new' => 12],
            ['id' => $engineTwo->id, 'new' => 15],
        ],
    ])
        ->assertForbidden();

    assertEquals(10, $engineOne->fresh()->rating);
    assertEquals(10, $engineTwo->fresh()->rating);
});

test('an authenticated user cannot update another users engine ratings', function () {
    $season = Season::factory()->creatE();
    [$engineOne, $engineTwo] = EngineSeason::factory(2)->for($season)->create(['rating' => 10]);

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.development.engines.store', [$season]), [
            'engines' => [
                ['id' => $engineOne->id, 'new' => 12],
                ['id' => $engineTwo->id, 'new' => 15],
            ],
        ])
        ->assertForbidden();

    $this->assertEquals(10, $engineOne->fresh()->rating);
    $this->assertEquals(10, $engineTwo->fresh()->rating);
});

test('the engine in a request must exist in the database', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.development.engines.store', [$season]), [
            'engines' => [
                ['id' => 'invalid id', 'new' => 10],
            ],
        ])
        ->assertSessionHasErrors(['engines.0.id' => 'The selected engines.0.id is invalid.']);
});

test('the new rating must be at least one', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $engine = EngineSeason::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.engines.store', [$season]), [
            'engines' => [
                ['id' => $engine->id, 'new' => 0],
            ],
        ])
        ->assertSessionHasErrors(['engines.0.new' => 'The engines.0.new must be at least 1.']);
});
