<?php

namespace App\Http\Controllers\Drivers;

use App\DataTransferObjects\StatData;
use App\Http\Controllers\Controller;
use App\Models\Driver;

class GetBaseStatsController extends Controller
{
    public function __invoke(Driver $driver)
    {
        $driver->load('results');

        return [
            new StatData('Career starts', $driver->results->count()),
            new StatData('Career poles', $driver->results->where('starting_position', 1)->count()),
            new StatData('Career podiums', $driver->results->whereIn('position', [1, 2, 3])->count()),
            new StatData('Career wins', $driver->results->where('position', 1)->count()),
        ];
    }
}
