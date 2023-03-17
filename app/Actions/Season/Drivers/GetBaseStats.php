<?php

namespace App\Actions\Season\Drivers;

use App\DataTransferObjects\StatData;
use App\Models\Driver;

class GetBaseStats
{
    public function __construct(
        protected readonly Driver $driver,
    ) {
        $this->driver->load('results', 'championshipResults.season');
    }

    public function handle(): array
    {
        return [
            new StatData('Starts', $this->driver->results->count()),
            new StatData('Poles', $this->driver->results->where('starting_position', 1)->count()),
            new StatData('Points', $this->driver->championshipResults->sum('points')),
            new StatData('Podiums', $this->driver->results->whereIn('position', [1, 2, 3])->count()),
            new StatData('Wins', $this->driver->results->where('position', 1)->count()),
            new StatData('Championships', $this->driver->championshipResults->where('position', 1)->count()),
        ];
    }
}
