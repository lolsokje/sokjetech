<?php

namespace App\Actions\Season\Copy;

use App\Models\Season;
use Gate;
use Illuminate\Validation\UnauthorizedException;

abstract class CopyAction
{
    protected ?array $columnsNotToCopy;

    public function __construct(
        protected readonly Season $oldSeason,
        protected readonly Season $newSeason,
    ) {
        $this->validateSeasonOwnership($this->oldSeason);
    }

    protected function validateSeasonOwnership(Season $season): void
    {
        if (! Gate::check('owns-universe', $season->universe)) {
            throw new UnauthorizedException;
        }
    }

    public function handle(): void
    {
        $this->validateSeasonRequirementsMet();
        $this->removeExistingModels();
        $this->copyModels();
    }

    abstract protected function validateSeasonRequirementsMet(): void;

    abstract protected function removeExistingModels(): void;

    abstract protected function copyModels(): void;
}
