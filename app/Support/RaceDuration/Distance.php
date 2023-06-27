<?php

namespace App\Support\RaceDuration;

use App\Contracts\RaceDuration;
use App\Enums\DistanceType;
use App\Support\Formatters\RaceDurationFormatter;

class Distance extends Duration implements RaceDuration
{
    public function editable(): string|int|array
    {
        return $this->race->distance_type === DistanceType::KM ?
            RaceDurationFormatter::metersToKilometers($this->race->duration) :
            RaceDurationFormatter::metersToMiles($this->race->duration);
    }

    public function readable(): string
    {
        $kilometers = RaceDurationFormatter::metersToKilometers($this->race->duration);
        $miles = RaceDurationFormatter::metersToMiles($this->race->duration);

        return "{$kilometers}km/{$miles}m";
    }

    public function postfix(): string
    {
        return '';
    }
}
