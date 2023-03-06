<?php

namespace App\Http\Controllers\Drivers;

use App\DataTransferObjects\StatData;
use App\Http\Controllers\Controller;
use App\Models\Driver;

class GetBaseStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        $driver->load('results', 'championshipResults.season');

        return [
            new StatData('Starts', $driver->results->count()),
            new StatData('Poles', $driver->results->where('starting_position', 1)->count()),
            new StatData('Points', $driver->championshipResults->sum('points')),
            new StatData('Podiums', $driver->results->whereIn('position', [1, 2, 3])->count()),
            new StatData('Wins', $driver->results->where('position', 1)->count()),
            new StatData('Championships', $driver->championshipResults->where('position', 1)->count()),
        ];
    }
}
