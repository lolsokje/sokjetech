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
