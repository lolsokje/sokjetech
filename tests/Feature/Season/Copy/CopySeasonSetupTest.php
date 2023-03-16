<?php

use App\Models\Season;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('a universe owner can view the copy season setup page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->get(route('seasons.settings.copy.index', [$season]))
        ->assertOk();
});

test('an unauthorized user cannot view the copy season setup page', function () {
    $season = Season::factory()->create();

    get(route('seasons.settings.copy.index', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->get(route('seasons.settings.copy.index', [$season]))
        ->assertForbidden();
});

test('a universe owner cannot view the copy season setup page on a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    actingAs($user)
        ->get(route('seasons.settings.copy.index', [$season]))
        ->assertRedirect(route('seasons.races.index', [$season]));
});
