<?php

use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\Enums\UniverseVisibility;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\RaceResult;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('shows the driver standings page for authenticated users', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user, UniverseVisibility::PRIVATE);

    RaceResult::factory(10)->for($season)->create();

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season)
    ))->handle();

    $this->actingAs($user)
        ->get(route('seasons.standings.drivers', $season))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Standings/Drivers')
            ->has('season', fn (AssertableInertia $prop) => $prop
                ->where('id', $season->id)
                ->etc())
            ->has('standings', 10));
});

it('does not show the driver standings page for unauthenticated users', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user, UniverseVisibility::PRIVATE);

    $this->get(route('seasons.standings.drivers', $season))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.standings.drivers', $season))
        ->assertForbidden();
});
