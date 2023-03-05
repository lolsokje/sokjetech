<?php

namespace App\Jobs;

use App\Contracts\CalculateDriverChampionshipContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateChampionshipStandingsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly CalculateDriverChampionshipContract $action,
    ) {
    }

    public function handle(): void
    {
        if (! $this->action->hasResults()) {
            return;
        }

        $this->action->clearExistingStandings();
        $this->action->cacheResults();
        $this->action->sortResults();
        $this->action->addPositionToResults();
        $this->action->storeStandings();
    }
}
