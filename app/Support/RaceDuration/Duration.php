<?php

namespace App\Support\RaceDuration;

use App\Models\Race;

class Duration
{
    public function __construct(
        protected readonly Race $race,
    ) {
    }
}
