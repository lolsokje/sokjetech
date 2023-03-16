<?php

namespace App\Actions\Season\Copy;

abstract class CopyWithRatingsAction extends CopyAction
{
    public function handle(
        ?bool $copyRatings = false,
    ): void {
        $this->columnsNotToCopy = $copyRatings ? null : ['rating', 'reliability'];

        $this->validateSeasonRequirementsMet();
        $this->removeExistingModels();
        $this->copyModels();
    }
}
