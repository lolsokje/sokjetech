<?php

namespace App\Console;

use App\Console\Commands\CreateDefaultCircuitVariationsCommand;
use App\Console\Commands\CreateStandingsCommand;
use App\Console\Commands\ResetRaceCommand;
use App\Console\Commands\ResetSeasonCommand;
use App\Console\Commands\UpdateQualifyingDetailsCommand;
use App\Console\Commands\UpdateQualifyingRunsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateStandingsCommand::class,
        UpdateQualifyingRunsCommand::class,
        UpdateQualifyingDetailsCommand::class,
        ResetRaceCommand::class,
        ResetSeasonCommand::class,
        CreateDefaultCircuitVariationsCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
