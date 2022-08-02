<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\followingRedirects;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('correctly determines universe ownership', function () {
    $universeOwner = User::factory()->create();
    $season = createSeasonForUser($universeOwner);

    $falseRequests = [
        get(route('universes.series.index', [$season->universe])),
        get(route('series.seasons.index', [$season->series])),
        followingRedirects()->get(route('series.seasons.show', [$season->series, $season])),
    ];

    actingAs($universeOwner);
    $trueRequests = [
        get(route('universes.series.index', [$season->universe])),
        get(route('series.seasons.index', [$season->series])),
        followingRedirects()->get(route('series.seasons.show', [$season->series, $season])),
    ];

    foreach ($falseRequests as $request) {
        assertFalse($request->inertiaPage()['props']['can']['edit']);
    }

    foreach ($trueRequests as $request) {
        assertTrue($request->inertiaPage()['props']['can']['edit']);
    }
});
