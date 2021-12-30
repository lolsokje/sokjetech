<?php

namespace Tests;

use App\Models\Series;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, LazilyRefreshDatabase;

    protected function createSeriesForUser(User $user): Series
    {
        return Series::factory()->for(Universe::factory()->for($user)->create())->create();
    }
}
