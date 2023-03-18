<?php

namespace App\Actions\Season\Copy;

use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Season;

class CopyEngines
{
    private array $engines = [];

    public function __construct(
        protected readonly Season $oldSeason,
        protected readonly Season $newSeason,
    ) {
    }

    public function handle(?array $columnsNotToCopy): void
    {
        foreach ($this->oldSeason->engines as $engine) {
            $newEngine = $engine->replicate($columnsNotToCopy);
            $newEngine->season()->associate($this->newSeason);

            $newEngine->save();

            $this->cacheEngine($engine, $newEngine);
        }
    }

    public function copyEngineToEntrant(Entrant $oldEntrant, Entrant $newEntrant): void
    {
        $newEngine = $this->engines[$oldEntrant->engine_id] ?? null;

        if (! $newEngine) {
            return;
        }

        $newEntrant->engine()->associate($newEngine);
        $newEntrant->save();
    }

    private function cacheEngine(EngineSeason $oldEngine, EngineSeason $newEngine): void
    {
        $this->engines[$oldEngine->id] = $newEngine;
    }
}
