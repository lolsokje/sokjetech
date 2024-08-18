<?php

use App\Enums\Season\Development\DevelopmentType;
use App\Models\Race;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Inertia\Testing\AssertableInertia;

it('loads the development page for universe owners', function (
    DevelopmentType $type,
    string $component,
    Factory $factory,
) {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->get(route('seasons.development.show', [$season, $type, $component]))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Development/Show', true)
            ->has('season', fn(AssertableInertia $prop) => $prop
                ->where('id', $season->id)
                ->etc())
            ->has('entities')
            ->where('type', $type->value)
            ->where('component', $component),
        );
})->with('development options');

test('unauthorised users cannot view the development page', function (
    DevelopmentType $type,
    string $component,
    Factory $factory,
    ?User $user,
) {
    $season = Season::factory()->create();

    if ($user) {
        $this->actingAs($user);
    }

    $this->get(route('seasons.development.show', [$season, $type, $component]))
        ->assertForbidden();
})->with('development options')->with([
    [null],
    [fn() => User::factory()->create()],
]);

it('correctly determines whether there is an active race', function (bool $started) {
    $season = Season::factory()->create();
    Race::factory()->for($season)->create(['qualifying_started' => $started]);

    $this->actingAs($season->universe->user)
        ->get(route('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating']))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->where('has_active_race', $started));
})->with([
    [true],
    [false],
]);

it('correctly determines whether there is a next race', function (bool $hasNextRace) {
    $season = Season::factory()->create();

    if ($hasNextRace) {
        Race::factory()->for($season)->create();
    }

    $this->actingAs($season->universe->user)
        ->get(route('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating']))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->where('has_next_race', $hasNextRace));
})->with([
    [true],
    [false],
]);
