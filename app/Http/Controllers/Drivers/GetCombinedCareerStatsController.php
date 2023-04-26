<?php

namespace App\Http\Controllers\Drivers;

use App\Actions\Season\Drivers\GetCareerResultStats;
use App\Http\Controllers\Controller;
use App\Models\Driver;

class GetCombinedCareerStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        return (new GetCareerResultStats($driver))->handle();
    }
}
