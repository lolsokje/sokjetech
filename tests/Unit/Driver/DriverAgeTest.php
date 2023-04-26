<?php

use App\Models\Driver;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;

it('calculates the age of a driver active in a season', function () {
    $driver = Driver::factory()->create(['dob' => '2000-07-31']);
    $season = Season::factory()->create(['year' => 2023]);
    /** @var Entrant $entrant */
    $entrant = Entrant::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->for($driver)->create(['entrant_id' => $entrant->id]);

    $this->assertEquals(23, $racer->age());
});

it('calculates the age of a driver for a given season', function () {
    $driver = Driver::factory()->create(['dob' => '2000-07-31']);
    $season = Season::factory()->create(['year' => 2023]);

    $this->assertEquals(23, $driver->age($season));
});
