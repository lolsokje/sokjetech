<?php

use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class)->in('Feature');

function createSeriesForUser(User $user): Series
{
    return Series::factory()->for(Universe::factory()->for($user)->create())->create();
}
