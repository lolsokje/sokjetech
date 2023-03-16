<?php

namespace App\DataTransferObjects\Configuration;

class QualifyingFormatConfiguration
{
    public function __construct(
        public readonly string $selectedFormat,
        public readonly array $formatDetails,
    ) {
    }
}
