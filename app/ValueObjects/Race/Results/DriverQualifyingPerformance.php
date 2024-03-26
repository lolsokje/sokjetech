<?php

namespace App\ValueObjects\Race\Results;

final readonly class DriverQualifyingPerformance
{
    public function __construct(
        public int $position,
        public array $runs,
    ) {
    }
}
