<?php

use App\Models\Race;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('universe owners can view the race weekend intro page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);
    $race = Race::factory()->for($season)->withStints()->create(['started' => true]);

    $this->actingAs($user)
        ->get(route('weekend.intro', $race))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('RaceWeekend/Intro')
            ->has('race', fn (AssertableInertia $prop) => $prop
                ->where('id', $race->id)
                ->where('season', $season->id)
                ->where('started', true)
                ->etc())
            ->has('stints', 3));
});

test('unauthorised users can not view the race weekend intro page', function () {
    $race = Race::factory()->create();

    $this->get(route('weekend.intro', $race))
        ->assertRedirect();

    $this->actingAs(User::factory()->create())
        ->get(route('weekend.intro', $race))
        ->assertRedirect();
});
