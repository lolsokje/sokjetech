<?php

namespace App\Console\Commands;

use App\Actions\Races\ResetRace;
use App\Models\Race;
use App\Models\Season;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Error;
use Illuminate\Console\View\Components\Info;

class ResetSeasonCommand extends Command
{
    protected $signature = 'season:reset {season}';

    protected $description = 'Completely resets all races within a season';

    public function handle(): void
    {
        if (config('app.env') !== 'local') {
            with(new Error($this->getOutput()))->render("Not in a local environment, aborting");
            exit(0);
        }

        $id = $this->argument('season');

        $season = Season::query()
            ->with(['races', 'series.universe.user'])
            ->find($id);

        if (! $season) {
            with(new Error($this->getOutput()))->render("Season with ID [$id] not found");
            exit(1);
        }

        $series = $season->series;
        $universe = $series->universe;
        $user = $universe->user;

        if (! $this->confirm(
            "Are you sure you want to reset 
            season <fg=red>$season->full_name</> (<fg=blue>$season->id</>)
            in series <fg=red>$series->name</> (<fg=blue>$series->id</>)
            in universe <fg=red>$universe->name</> (<fg=blue>$universe->id</>)
            by user <fg=red>$user->username</> (<fg=blue>$user->id</>)?",
        )) {
            with(new Info($this->getOutput()))->render("Aborting");
            exit(0);
        }

        $resetRaceAction = new ResetRace;

        $season->races->each(fn (Race $race) => $resetRaceAction->handle($race));

        $season->driverChampionshipStandings()->delete();
        $season->teamChampionshipStandings()->delete();

        $season->update([
            'started' => false,
            'completed' => false,
        ]);

        with(new Info($this->getOutput()))->render("Season [$season->name] reset");
    }
}
