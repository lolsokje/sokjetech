<?php

use App\Models\Entrant;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can view the team development page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(4)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.development.teams', [$season]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Development/Teams')
            ->has('season')
            ->has('teams', 4)
        );
});

test('an unauthorized user cant view the team development page', function () {
    $season = Season::factory()->create();
    Entrant::factory(4)->for($season)->create();

    $this->get(route('seasons.development.teams', [$season]))
        ->assertForbidden();
});

test('an authorized user cant view the team development page for another users universe', function () {
    $season = Season::factory()->create();
    Entrant::factory(4)->for($season)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.development.teams', [$season]))
        ->assertForbidden();
});

test('a universe owner can update team ratings', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    [$entrantOne, $entrantTwo] = Entrant::factory(2)->for($season)->create(['rating' => 30]);

    $this->actingAs($user)
        ->post(route('seasons.development.teams.store', [$season]), [
            'teams' => [
                ['id' => $entrantOne->id, 'new' => 60],
                ['id' => $entrantTwo->id, 'new' => 55],
            ],
        ])
        ->assertRedirect(route('seasons.development.teams', [$season]));

    $this->assertEquals(60, $entrantOne->fresh()->rating);
    $this->assertEquals(55, $entrantTwo->fresh()->rating);
});

test('an unauthenticated user cant update team ratings', function () {
    $season = Season::factory()->create();
    [$racerOne, $racerTwo] = Entrant::factory(2)->for($season)->create(['rating' => 30]);

    $this->post(route('seasons.development.teams.store', [$season]), [
        'teams' => [
            ['id' => $racerOne->id, 'new' => 60],
            ['id' => $racerTwo->id, 'new' => 55],
        ],
    ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->rating);
    $this->assertEquals(30, $racerTwo->fresh()->rating);
});

test('an authenticated user cant update another users team ratings', function () {
    $season = Season::factory()->creatE();
    [$racerOne, $racerTwo] = Entrant::factory(2)->for($season)->create(['rating' => 30]);

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.development.teams.store', [$season]), [
            'teams' => [
                ['id' => $racerOne->id, 'new' => 60],
                ['id' => $racerTwo->id, 'new' => 55],
            ],
        ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->rating);
    $this->assertEquals(30, $racerTwo->fresh()->rating);
});

test('the team in a request must exist in the database', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.development.teams.store', [$season]), [
            'teams' => [
                ['id' => 'invalid id', 'new' => 60],
            ],
        ])
        ->assertSessionHasErrors(['teams.0.id' => 'The selected teams.0.id is invalid.']);
});

test('the new rating must be at least one', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $racer = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.teams.store', [$season]), [
            'teams' => [
                ['id' => $racer->id, 'new' => 0],
            ],
        ])
        ->assertSessionHasErrors(['teams.0.new' => 'The teams.0.new must be at least 1.']);
});
