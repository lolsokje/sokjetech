<?php

use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class)->in('Feature', 'Unit');

function createSeriesForUser(User $user): Series
{
    return Series::factory()->for(Universe::factory()->for($user)->create())->create();
}

function createSeasonForUser(User $user): Season
{
    $series = createSeriesForUser($user);

    return Season::factory()->for($series)->create();
}

function prepareSeason(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Racer::factory(5)->for($season)->create();
    $race = Race::factory()->for($season)->create();
    $format = ThreeSessionElimination::factory()->create();

    $season->qualifyingFormat()->associate($format);
    $season->update(['started' => true]);

    return [
        $user,
        $drivers,
        $race,
    ];
}
