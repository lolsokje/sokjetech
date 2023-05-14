<?php

namespace App\Console\Commands;

use App\Models\Race;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Info;

class UpdateQualifyingRunsCommand extends Command
{
    protected $signature = 'qualifying:update';

    protected $description = 'Temporary command to quickly update qualifying results to the new format';

    public function handle(): void
    {
        $environment = config('app.env');
        if (! $this->confirm("Are you sure you want to update all qualifying results on the <fg=red>$environment</> environment?")) {
            with(new Info($this->getOutput()))->render("Updating aborted");
            exit(0);
        }

        $races = Race::with('qualifyingResults')->get();

        foreach ($races as $race) {
            $qualifyingResults = $race->qualifyingResults;

            $allResults = [];

            foreach ($qualifyingResults as $result) {
                $data = [
                    'rating' => $result->driver_rating + $result->engine_rating + $result->team_rating,
                    'runs' => [],
                ];

                $sessions = $result->runs;

                foreach ($sessions as $session => $runs) {
                    if (in_array([], $runs)) {
                        continue;
                    }
                    $data['runs'][$session + 1] = [
                        'position' => null,
                        'runs' => $runs,
                    ];
                }

                $allResults[$result->racer_id] = $data;
            }

            $sessions = [1, 2, 3];

            foreach ($sessions as $session) {
                $sessionBests = [];

                foreach ($allResults as $racerId => $result) {
                    if (! array_key_exists($session, $result['runs'])) {
                        continue;
                    }

                    $runs = $result['runs'][$session];

                    if (! count($runs['runs'])) {
                        continue;
                    }

                    $best = max($runs['runs']);
                    if (is_array($best)) {
                        continue;
                    }
                    $total = $result['rating'] + $best;

                    $sessionBests[$racerId] = $total;
                }

                arsort($sessionBests);

                foreach (array_keys($sessionBests) as $position => $racerId) {
                    $allResults[$racerId]['runs'][$session]['position'] = $position + 1;
                }
            }

            foreach ($allResults as $racerId => $result) {
                $qualifyingResults->where('racer_id', $racerId)->first()->update(['runs' => $result['runs']]);
            }
        }
    }
}
