<?php

namespace App\ValueObjects\Race\Results;

final readonly class DriverRatings
{
    public int $totalRating;
    public int $startingTotal;

    public function __construct(
        public int $driverRating,
        public ?int $driverReliability,
        public int $teamRating,
        public ?int $teamReliability,
        public int $engineRating,
        public ?int $engineReliability,
        public int $startingBonus,
    ) {
        $this->totalRating = $this->driverRating + $this->teamRating + $this->engineRating;
        $this->startingTotal = $this->totalRating + $this->startingBonus;
    }
}
