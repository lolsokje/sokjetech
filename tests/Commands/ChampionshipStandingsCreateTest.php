<?php

use App\Models\RaceResult;

it('creates standings', function () {
    RaceResult::factory()->create();

    $this->assertDatabaseCount('driver_championship_standings', 0);
    $this->assertDatabaseCount('team_championship_standings', 0);

    $this->artisan('standings:create');

    $this->assertDatabaseCount('driver_championship_standings', 1);
    $this->assertDatabaseCount('team_championship_standings', 1);
});
