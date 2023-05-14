<?php

namespace App\DataTransferObjects\RaceWeekend;

class DriverRatings
{
    public int $driverRating;

    public int $teamRating;

    public ?int $engineRating;

    public function __construct(array $driver)
    {
        $this->driverRating = $driver['ratings']['driver_rating'];
        $this->teamRating = $driver['ratings']['team_rating'];
        $this->engineRating = $driver['ratings']['engine_rating'];
    }
}
