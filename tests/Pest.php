<?php

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
