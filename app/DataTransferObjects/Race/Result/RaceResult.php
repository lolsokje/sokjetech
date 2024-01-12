<?php

namespace App\DataTransferObjects\Race\Result;

final readonly class RaceResult
{
    public function __construct(
        public int $id,
        public int $position,
        public int $total,
        public array $stints,
    ) {
    }
}
