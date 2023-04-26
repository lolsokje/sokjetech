<?php

namespace App\Actions\Season\Copy;

use App\Models\Race;

class CopyStintsAction
{
    public function __construct(
        protected readonly bool $copyStints,
    ) {
    }

    public function handle(
        Race $oldRace,
        Race $newRace,
    ): void {
        if (! $this->copyStints) {
            return;
        }

        foreach ($oldRace->stints as $oldStint) {
            $newStint = $oldStint->replicate();
            $newStint->race()->associate($newRace);
            $newStint->save();
        }
    }
}
