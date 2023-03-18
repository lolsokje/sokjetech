<?php

use App\Models\Season;
use App\Models\User;

test('a universe owner can delete a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->delete(route('series.seasons.destroy', [$season->series, $season]))
        ->assertOk();

    $this->assertDatabaseMissing('seasons', ['id' => $season->id]);
});

test('unauthorised users can not delete seasons', function () {
    $season = Season::factory()->create();

    $this->delete(route('series.seasons.destroy', [$season->series, $season]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->delete(route('series.seasons.destroy', [$season->series, $season]))
        ->assertForbidden();

    $this->assertDatabaseHas('seasons', ['id' => $season->id]);
});

test('authenticated users can not delete seasons belonging to other users', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    $this->actingAs($user)
        ->delete(route('series.seasons.destroy', [$season->series, $season]))
        ->assertForbidden();

    $this->assertDatabaseHas('seasons', ['id' => $season->id]);
});
