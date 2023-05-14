<?php

namespace App\Console\Commands;

use App\Actions\Races\ResetRace;
use App\Models\Race;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Error;
use Illuminate\Console\View\Components\Info;

class ResetRaceCommand extends Command
{
    protected $signature = 'race:reset {race}';

    protected $description = 'Completely resets a race';

    public function handle(): void
    {
        if (config('app.env') !== 'local') {
            with(new Error($this->getOutput()))->render("Not in a local environment, aborting");
            exit(0);
        }

        $id = $this->argument('race');

        $race = Race::query()
            ->with('season.series.universe.user')
            ->find($id);

        if (! $race) {
            with(new Error($this->getOutput()))->render("Race with ID [$id] not found");
            exit(1);
        }

        $season = $race->season;
        $series = $season->series;
        $universe = $series->universe;
        $user = $universe->user;

        if (! $this->confirm(
            "Are you sure you want to reset 
            race <fg=red>$race->name</> (<fg=blue>$race->id</>)
            in season <fg=red>$season->full_name</> (<fg=blue>$season->id</>)
            in series <fg=red>$series->name</> (<fg=blue>$series->id</>)
            in universe <fg=red>$universe->name</> (<fg=blue>$universe->id</>)
            by user <fg=red>$user->username</> (<fg=blue>$user->id</>)?",
        )) {
            with(new Info($this->getOutput()))->render("Aborting");
            exit(0);
        }

        (new ResetRace)->handle($race);

        with(new Info($this->getOutput()))->render("Race [$race->name] reset");
    }
}
