<?php

namespace App\Console\Commands;

use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Race;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Info;

class UpdateQualifyingDetailsCommand extends Command
{
    protected $signature = 'qualifying:update_details';

    protected $description = 'Temporary command to quickly update race qualifying details';

    public function handle(): void
    {
        $environment = config('app.env');
        if (! $this->confirm("Are you sure you want to update all race qualifying details on the <fg=red>$environment</> environment?")) {
            with(new Info($this->getOutput()))->render("Updating aborted");
            exit(0);
        }

        $races = Race::query()
            ->whereRelation('season', 'format_type', ThreeSessionElimination::class)->with('season')->get();

        foreach ($races as $race) {
            $details = $race->qualifying_details;

            if ($details === null) {
                continue;
            }

            $details['current_session'] += 1;

            $race->update(['qualifying_details' => $details]);
        }
    }
}
