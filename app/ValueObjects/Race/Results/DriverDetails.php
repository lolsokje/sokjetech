<?php

namespace App\ValueObjects\Race\Results;

final readonly class DriverDetails
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public int $number,
    ) {
    }
}
