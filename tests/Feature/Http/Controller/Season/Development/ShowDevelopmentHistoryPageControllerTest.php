<?php

use App\Enums\Season\Development\DevelopmentType;
use App\Models\Race;
use App\Models\Season;
use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Inertia\Testing\AssertableInertia;

it('loads the development history page for universe owners', function (
    DevelopmentType $type,
    string $component,
    Factory $factory,
) {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory()->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.history.show', [$season, $type, $component]))
        ->assertOk()
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Development/History', true)
            ->has('season', fn(AssertableInertia $prop) => $prop
                ->where('id', $season->id)
                ->etc())
            ->has('results')
            ->has('races', 1)
            ->where('type', $type->value)
            ->where('component', $component),
        );
})->with('development options');

test('unauthorised users cannot view the development history page', function (
    DevelopmentType $type,
    string $component,
    Factory $factory,
    ?User $user,
) {
    $universe = Universe::factory()->private()->create();
    $series = Series::factory()->for($universe)->create();
    $season = Season::factory()->for($series)->create();

    if ($user) {
        $this->actingAs($user);
    }

    $this->get(route('seasons.history.show', [$season, $type, $component]))
        ->assertForbidden();
})->with('development options')->with([
    [null],
    [fn() => User::factory()->create()],
]);
