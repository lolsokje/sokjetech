<?php

namespace App\DataTransferObjects\Configuration;

class ReliabilityConfiguration
{
    public function __construct(
        public readonly int $minRng,
        public readonly int $maxRng,
        public readonly array $reasons,
    ) {
    }
}
