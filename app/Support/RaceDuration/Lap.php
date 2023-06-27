<?php

namespace App\Support\RaceDuration;

use App\Contracts\RaceDuration;

class Lap extends Duration implements RaceDuration
{
    public function editable(): string|int|array
    {
        return $this->race->duration;
    }

    public function readable(): string
    {
        return $this->race->duration;
    }

    public function postfix(): string
    {
        return 'laps';
    }
}
