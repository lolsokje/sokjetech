<?php

namespace App\Console\Commands;

use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use Illuminate\Console\Command;

class SeasonDevelopmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs team, driver and engine development for a newly created season';

    private array $developmentEnvironments = [
        'local',
        'testing',
    ];

    protected ?Season $season = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! in_array(config('app.env'), $this->developmentEnvironments) && ! config('app.debug')) {
            $this->error('Not in a local development environment, aborting');

            return 1;
        }

        if (! $this->confirm('Are you sure you want to completely reset and re-run dev?')) {
            $this->info('Aborting');

            return 1;
        }

        if (count(Season::all()) !== 1) {
            $this->error('More or less than one season exists in the database, aborting');

            return 1;
        }

        $this->season = Season::first();

        $this->resetAllDevelopment();

        $this->engineDevelopment();
        $this->teamDevelopment();
        $this->driverDevelopment();

        $this->info('Development applied');

        return 0;
    }

    private function resetAllDevelopment(): void
    {
        $this->season->engines()->each(fn (EngineSeason $engine) => $engine->update([
            'rating' => 0,
            'reliability' => 0,
        ]));

        $this->season->entrants()->each(fn (Entrant $entrant) => $entrant->update([
            'rating' => 0,
            'reliability' => 0,
        ]));

        $this->season->drivers()->each(fn (Racer $driver) => $driver->update(['rating' => 0]));
    }

    private function engineDevelopment(): void
    {
        $this->season->engines()->each(function (EngineSeason $engine) {
            $engine->update([
                'rating' => rand(10, 25),
                'reliability' => rand(90, 100),
            ]);
        });
    }

    private function teamDevelopment(): void
    {
        $this->season->entrants()->each(function (Entrant $entrant) {
            $entrant->update([
                'rating' => rand(40, 70),
                'reliability' => rand(90, 100),
            ]);
        });
    }

    private function driverDevelopment(): void
    {
        $this->season->drivers()->each(function (Racer $driver) {
            $driver->update([
                'rating' => rand(40, 60),
                'reliability' => rand(90, 100),
            ]);
        });
    }
}
