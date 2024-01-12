<?php

namespace App\ValueObjects\Race\Results;

final readonly class TeamDetails
{
    public function __construct(
        public string $name,
        public string $styleString,
        public string $accentColour,
    ) {
    }
}
