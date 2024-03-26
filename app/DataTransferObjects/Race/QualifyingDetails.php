<?php

namespace App\DataTransferObjects\Race;

final readonly class QualifyingDetails
{
    public function __construct(
        public int $session,
        public int $run,
    ) {
    }
}
