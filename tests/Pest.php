<?php

use App\Enums\UniverseVisibility;
use App\Models\PointSystem;
use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class)->in('Feature', 'Unit', 'Commands');

function createSeriesForUser(User $user, ?UniverseVisibility $visibility = UniverseVisibility::PUBLIC): Series
{
    return Series::factory()
        ->for(
            Universe::factory()->for($user)->create([
                'visibility' => $visibility,
            ]),
        )
        ->create();
}

function createSeasonForUser(User $user, ?UniverseVisibility $visibility = UniverseVisibility::PUBLIC): Season
{
    $series = createSeriesForUser($user, $visibility);

    return Season::factory()->for($series)->create();
}

function prepareSeason(?int $drivers = 5): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Racer::factory($drivers)->for($season)->create();
    $race = Race::factory()->for($season)->create();
    $format = ThreeSessionElimination::factory()->create();

    $season->qualifyingFormat()->associate($format);
    $season->update(['started' => true]);

    PointSystem::factory()->for($season)->create();

    return [
        $user,
        $drivers,
        $race,
    ];
}
