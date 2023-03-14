<?php

namespace App\Actions\Season;

use App\Models\Season;
use Gate;
use Illuminate\Validation\UnauthorizedException;

class BaseCopyAction
{
    protected ?array $columnsNotToCopy;

    public function __construct(
        protected readonly Season $oldSeason,
        protected readonly Season $newSeason,
        protected readonly bool $copyRatings,
    ) {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->columnsNotToCopy = $this->copyRatings ? null : ['rating', 'reliability'];
    }

    protected function validateSeasonOwnership(Season $season): void
    {
        if (! Gate::check('owns-universe', $season->universe)) {
            throw new UnauthorizedException;
        }
    }
}
