<?php

namespace App\ValueObjects\Race\Results;

final readonly class DriverPerformance
{
    public int $stintsTotal;

    public function __construct(
        public int $startingPosition,
        public int $position,
        public int $positionChange,
        public array $stints,
        public int $raceTotal,
        public ?int $fastestLapRoll,
        public ?string $dnf,
        public bool $fastestLap,
        public int $points,
    ) {
        $this->stintsTotal = $this->getStintTotal();
    }

    private function getStintTotal(): int
    {
        return (int) collect($this->stints)->reduce(fn (int $sum, int $current) => $sum + $current, 0);
    }
}
