<?php

namespace App\DataTransferObjects\Race\Result;

final readonly class RaceResultCollection
{
    /**
     * @param array<RaceResult> $results
     */
    public function __construct(
        public array $results,
    ) {
    }
}
