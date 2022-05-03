<?php

use App\Models\Entrant;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can view the team reliability page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(4)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.development.reliability.teams', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Development/Reliability/Teams')
                ->has('season')
                ->has('teams', 4)
        );
});

test('an unauthorized user cant view the team reliability page', function () {
    $season = Season::factory()->create();
    Entrant::factory(4)->for($season)->create();

    $this->get(route('seasons.development.reliability.teams', [$season]))
        ->assertForbidden();
});

test('an authorized user cant view the team reliability page for another users universe', function () {
    $season = Season::factory()->create();
    Entrant::factory(4)->for($season)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.development.reliability.teams', [$season]))
        ->assertForbidden();
});

test('a universe owner can update team reliability', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    [$entrantOne, $entrantTwo] = Entrant::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.teams.store', [$season]), [
            'teams' => [
                ['id' => $entrantOne->id, 'new' => 60],
                ['id' => $entrantTwo->id, 'new' => 55],
            ],
        ])
        ->assertRedirect(route('seasons.development.reliability.teams', [$season]));

    $this->assertEquals(60, $entrantOne->fresh()->reliability);
    $this->assertEquals(55, $entrantTwo->fresh()->reliability);
});

test('an unauthenticated user cant update team reliability', function () {
    $season = Season::factory()->create();
    [$racerOne, $racerTwo] = Entrant::factory(2)->for($season)->create(['reliability' => 30]);

    $this->post(route('seasons.development.reliability.teams.store', [$season]), [
        'teams' => [
            ['id' => $racerOne->id, 'new' => 60],
            ['id' => $racerTwo->id, 'new' => 55],
        ],
    ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->reliability);
    $this->assertEquals(30, $racerTwo->fresh()->reliability);
});

test('an authenticated user cant update another users team reliability', function () {
    $season = Season::factory()->creatE();
    [$racerOne, $racerTwo] = Entrant::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.development.reliability.teams.store', [$season]), [
            'teams' => [
                ['id' => $racerOne->id, 'new' => 60],
                ['id' => $racerTwo->id, 'new' => 55],
            ],
        ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->reliability);
    $this->assertEquals(30, $racerTwo->fresh()->reliability);
});

test('the team in a request must exist in the database', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.teams.store', [$season]), [
            'teams' => [
                ['id' => 'invalid id', 'new' => 60],
            ],
        ])
        ->assertSessionHasErrors(['teams.0.id' => 'The selected teams.0.id is invalid.']);
});

test('the new reliability must be at least one', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $racer = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.teams.store', [$season]), [
            'teams' => [
                ['id' => $racer->id, 'new' => 0],
            ],
        ])
        ->assertSessionHasErrors(['teams.0.new' => 'The teams.0.new must be at least 1.']);
});
