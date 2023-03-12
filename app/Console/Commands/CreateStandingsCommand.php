<?php

namespace App\Console\Commands;

use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\Actions\Season\Standings\CalculateTeamChampionshipStandingsAction;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\Season;
use Illuminate\Console\Command;

class CreateStandingsCommand extends Command
{
    protected $signature = 'standings:create';

    protected $description = 'Pushes jobs to the queue to create standings for all seasons';

    public function handle(): void
    {
        foreach (Season::all() as $season) {
            CalculateChampionshipStandingsJob::dispatch(
                new CalculateDriverChampionshipStandingsAction($season),
            );

            CalculateChampionshipStandingsJob::dispatch(
                new CalculateTeamChampionshipStandingsAction($season),
            );
        }

        $this->info("Jobs created");
    }
}
