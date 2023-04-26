<?php

namespace App\Http\Controllers\Drivers;

use App\Actions\Season\Drivers\GetBaseStats;
use App\Http\Controllers\Controller;
use App\Models\Driver;

class GetBaseStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        return (new GetBaseStats($driver))->handle();
    }
}
