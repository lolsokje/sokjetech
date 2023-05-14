<?php

namespace App\DataTransferObjects\RaceWeekend;

readonly class QualifyingDriverResult
{
    public ?int $position;

    public array $sessions;

    public function __construct(array $driver)
    {
        $this->position = $driver['result']['position'];
        $this->sessions = $driver['result']['sessions'];
    }
}
