<?php

use App\Models\Stint;
use App\Models\User;

it('shows stints grouped by attributes', function () {
    Stint::factory(10)->sequence(
        ['min_rng' => 5, 'use_team_rating' => true],
        ['min_rng' => 10, 'use_team_rating' => true],
        ['min_rng' => 5, 'use_team_rating' => false],
        ['min_rng' => 10, 'use_team_rating' => false],
    )->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('stints'))
        ->assertJsonCount(4);
});

it('does not show stints outside the requested RNG range', function () {
    Stint::factory(3)->sequence(
        ['min_rng' => 5, 'max_rng' => 30],
        ['min_rng' => 20, 'max_rng' => 35],
        ['min_rng' => 0, 'max_rng' => 20],
    )->create();

    $this->actingAs(User::factory()->create())
        ->get(route('stints'))
        ->assertJsonCount(3);

    $this->actingAs(User::factory()->create())
        ->get(route('stints', ['min_rng' => 0, 'max_rng' => 40]))
        ->assertJsonCount(3);

    $this->get(route('stints', ['min_rng' => 5, 'max_rng' => 40]))
        ->assertJsonCount(2);

    $this->get(route('stints', ['min_rng' => 0, 'max_rng' => 30]))
        ->assertJsonCount(2);

    $this->get(route('stints', ['min_rng' => 15, 'max_rng' => 35]))
        ->assertJsonCount(1);

    $this->get(route('stints', ['min_rng' => 10, 'max_rng' => 15]))
        ->assertJsonCount(0);
});

it('does not show stints failing the requested reliability', function () {
    Stint::factory(20)->sequence(
        ['reliability' => true],
        ['reliability' => false],
    )->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('stints', ['reliability' => 'false']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'reliability' => false,
        ]);

    $this->get(route('stints', ['reliability' => 'true']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'reliability' => true,
        ]);
});

it('does not show stints failing the request use of team rating', function () {
    Stint::factory(20)->sequence(
        ['use_team_rating' => true],
        ['use_team_rating' => false],
    )->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('stints', ['use_team_rating' => 'false']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_team_rating' => false,
        ]);

    $this->get(route('stints', ['use_team_rating' => 'true']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_team_rating' => true,
        ]);
});

it('does not show stints failing the request use of driver rating', function () {
    Stint::factory(20)->sequence(
        ['use_driver_rating' => true],
        ['use_driver_rating' => false],
    )->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('stints', ['use_driver_rating' => 'false']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_driver_rating' => false,
        ]);

    $this->get(route('stints', ['use_driver_rating' => 'true']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_driver_rating' => true,
        ]);
});

it('does not show stints failing the request use of engine rating', function () {
    Stint::factory(20)->sequence(
        ['use_engine_rating' => true],
        ['use_engine_rating' => false],
    )->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('stints', ['use_engine_rating' => 'false']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_engine_rating' => false,
        ]);

    $this->get(route('stints', ['use_engine_rating' => 'true']))
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'use_engine_rating' => true,
        ]);
});
