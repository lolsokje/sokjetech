<?php

namespace App\Support\RaceDuration;

use App\Contracts\RaceDuration;
use App\Support\Formatters\RaceDurationFormatter;
use Str;

class Time extends Duration implements RaceDuration
{
    public function editable(): string|int|array
    {
        return RaceDurationFormatter::toHoursAndMinutes($this->race->duration);
    }

    public function readable(): string
    {
        [$hours, $minutes] = $this->editable();

        $minutes = Str::padLeft($minutes, 2, '0');

        return "{$hours}h{$minutes}m";
    }

    public function postfix(): string
    {
        return '';
    }
}
