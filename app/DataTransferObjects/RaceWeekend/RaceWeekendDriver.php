<?php

namespace App\DataTransferObjects\RaceWeekend;

readonly class RaceWeekendDriver
{
    public int $id;

    public ?int $entrantId;

    public DriverRatings $rating;

    public function __construct(array $driver)
    {
        $this->id = $driver['id'];
        $this->entrantId = $driver['entrant_id'] ?? null;
        $this->rating = new DriverRatings($driver);
    }
}
