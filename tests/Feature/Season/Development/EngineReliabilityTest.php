<?php

use App\Models\EngineSeason;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can view the engine reliability page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    EngineSeason::factory(4)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.development.reliability.engines', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Development/Reliability/Engines')
                ->has('season')
                ->has('engines', 4),
        );
});

test('an unauthorized user cant view the engine reliability page', function () {
    $season = Season::factory()->create();
    Racer::factory(4)->for($season)->create();


    $this->get(route('seasons.development.reliability.engines', [$season]))
        ->assertForbidden();
});

test('an authorized user cant view the engine reliability page for another users universe', function () {
    $season = Season::factory()->create();
    Racer::factory(4)->for($season)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.development.reliability.engines', [$season]))
        ->assertForbidden();
});

test('a universe owner can update engine reliability', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    [$engineOne, $engineTwo] = EngineSeason::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.engines.store', [$season]), [
            'engines' => [
                ['id' => $engineOne->id, 'new' => 60],
                ['id' => $engineTwo->id, 'new' => 55],
            ],
        ])
        ->assertRedirect(route('seasons.development.reliability.engines', [$season]));

    $this->assertEquals(60, $engineOne->fresh()->reliability);
    $this->assertEquals(55, $engineTwo->fresh()->reliability);
});

test('an unauthenticated user cant update engine reliability', function () {
    $season = Season::factory()->create();
    [$racerOne, $racerTwo] = Racer::factory(2)->for($season)->create(['reliability' => 30]);

    $this->post(route('seasons.development.reliability.engines.store', [$season]), [
        'engines' => [
            ['id' => $racerOne->id, 'new' => 60],
            ['id' => $racerTwo->id, 'new' => 55],
        ],
    ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->reliability);
    $this->assertEquals(30, $racerTwo->fresh()->reliability);
});

test('an authenticated user cant update another users engine reliability', function () {
    $season = Season::factory()->creatE();
    [$engineOne, $engineTwo] = EngineSeason::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.development.reliability.engines.store', [$season]), [
            'engines' => [
                ['id' => $engineOne->id, 'new' => 60],
                ['id' => $engineTwo->id, 'new' => 55],
            ],
        ])
        ->assertForbidden();

    $this->assertEquals(30, $engineOne->fresh()->reliability);
    $this->assertEquals(30, $engineTwo->fresh()->reliability);
});

test('the engine in a request must exist in the database', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.engines.store', [$season]), [
            'engines' => [
                ['id' => 'invalid id', 'new' => 60],
            ],
        ])
        ->assertSessionHasErrors(['engines.0.id' => 'The selected engines.0.id is invalid.']);
});

test('the new reliability must be at least one', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $racer = Racer::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.engines.store', [$season]), [
            'engines' => [
                ['id' => $racer->id, 'new' => 0],
            ],
        ])
        ->assertSessionHasErrors(['engines.0.new' => 'The engines.0.new must be at least 1.']);
});
