<?php

use App\Models\Driver;
use App\Models\Entrant;
use App\Models\User;

test('a universe owner can add drivers to entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season]), [
            'driver_id' => $driver->id,
            'entrant_id' => $entrant->id,
            'number' => 2,
        ])
        ->assertRedirect(route('seasons.lineups.index', [$season]));

    $this->assertDatabaseCount('lineups', 1);
    $this->assertCount(1, $entrant->drivers);
    $this->assertCount(1, $season->drivers);
});
