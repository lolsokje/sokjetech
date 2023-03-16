<?php

use App\Models\Season;

it('does not run in non dev environments', function () {
    Config::set('app.env', 'invalid');
    Config::set('app.debug', false);
    $this->artisan('season:dev')->assertFailed();
});

it('does not run if confirmation is denied', function () {
    $this->artisan('season:dev')
        ->expectsConfirmation('Are you sure you want to completely reset and re-run dev?', 'No')
        ->assertFailed();
});

it('does not run if fewer or more than one season exists in the database', function () {
    $this->artisan('season:dev')
        ->expectsConfirmation('Are you sure you want to completely reset and re-run dev?', 'Yes')
        ->assertFailed();

    Season::factory(2)->create();

    $this->artisan('season:dev')
        ->expectsConfirmation('Are you sure you want to completely reset and re-run dev?', 'Yes')
        ->assertFailed();
});

it('performs dev if one season exists', function () {
    $this->artisan('season:seed')
        ->expectsConfirmation('This will clear the entire database, are you sure you want to continue?', 'yes');

    $season = Season::first();

    $this->assertEquals(0, $season->entrants()->first()->rating);
    $this->assertEquals(0, $season->entrants()->first()->reliability);

    $this->assertEquals(0, $season->drivers()->first()->rating);
    $this->assertEquals(0, $season->drivers()->first()->reliability);

    $this->assertEquals(0, $season->engines()->first()->rating);
    $this->assertEquals(0, $season->engines()->first()->reliability);

    $this->artisan('season:dev')
        ->expectsConfirmation('Are you sure you want to completely reset and re-run dev?', 'Yes')
        ->assertOk();


    $this->assertNotEquals(0, $season->entrants()->first()->rating);
    $this->assertNotEquals(0, $season->entrants()->first()->reliability);

    $this->assertNotEquals(0, $season->drivers()->first()->rating);
    $this->assertNotEquals(0, $season->drivers()->first()->reliability);

    $this->assertNotEquals(0, $season->engines()->first()->rating);
    $this->assertNotEquals(0, $season->engines()->first()->reliability);
});
