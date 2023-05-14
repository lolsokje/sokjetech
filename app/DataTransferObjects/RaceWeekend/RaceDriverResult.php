<?php

namespace App\DataTransferObjects\RaceWeekend;

readonly class RaceDriverResult
{
    public int $position;

    public int $startingPosition;

    public int $startingBonus;

    public ?string $dnf;

    public bool $fastestLap;

    public ?int $fastestLapRoll;

    public array $stints;

    public function __construct(array $driver)
    {
        $this->position = $driver['result']['position'];
        $this->startingPosition = $driver['result']['starting_position'];
        $this->startingBonus = $driver['result']['starting_bonus'];
        $this->dnf = $driver['result']['dnf'];
        $this->fastestLap = $driver['result']['fastest_lap'];
        $this->fastestLapRoll = $driver['result']['fastest_lap_roll'];
        $this->stints = $driver['result']['stints'];
    }
}
