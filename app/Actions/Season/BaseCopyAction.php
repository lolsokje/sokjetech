<?php

namespace App\Actions\Season;

use App\Models\Season;
use Gate;
use Illuminate\Validation\UnauthorizedException;

class BaseCopyAction
{
    protected function validateSeasonOwnership(Season $season): void
    {
        if (!Gate::check('owns-universe', $season->universe)) {
            throw new UnauthorizedException();
        }
    }
}
