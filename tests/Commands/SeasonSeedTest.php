<?php

it('does not run in non dev environments', function () {
    Config::set('app.env', 'invalid');
    Config::set('app.debug', false);
    $this->artisan('season:seed')->assertFailed();
});

it('does not run if confirmation is denied', function () {
    $this->artisan('season:seed')
        ->expectsConfirmation('This will clear the entire database, are you sure you want to continue?', 'No')
        ->assertFailed();
});

it('seeds the database', function () {
    $this->artisan('season:seed')
        ->expectsConfirmation('This will clear the entire database, are you sure you want to continue?', 'yes')
        ->assertOk();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('universes', 1);
    $this->assertDatabaseCount('series', 1);
    $this->assertDatabaseCount('seasons', 1);

    $this->assertDatabaseCount('teams', 10);
    $this->assertDatabaseCount('drivers', 20);
    $this->assertDatabaseCount('engines', 4);
    $this->assertDatabaseCount('circuits', 22);

    $this->assertDatabaseCount('entrants', 10);
    $this->assertDatabaseCount('racers', 20);
    $this->assertDatabaseCount('engine_seasons', 4);
    $this->assertDatabaseCount('races', 22);
});
