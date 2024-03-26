<?php

namespace App\DataTransferObjects\RaceWeekend;

class QualifyingDriverResult
{
    public ?int $position;

    public array $sessions;

    public function __construct(array $driver)
    {
        $this->position = $driver['performance']['position'];
        $this->sessions = $driver['performance']['sessions'];
    }
}
