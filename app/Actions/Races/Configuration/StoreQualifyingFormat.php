<?php

namespace App\Actions\Races\Configuration;

use App\DataTransferObjects\Configuration\QualifyingFormatConfiguration;
use App\Enums\QualifyingFormat;
use App\Models\Season;

class StoreQualifyingFormat
{
    public function __construct(
        protected readonly QualifyingFormatConfiguration $configuration,
        protected readonly Season $season,
    ) {
    }

    public function handle(): void
    {
        $format = QualifyingFormat::tryFrom($this->configuration->selectedFormat);

        $this->season->qualifyingFormat?->delete();

        $class = $format->modelFullyQualifiedClassName();

        $savedFormat = $class::create($this->configuration->formatDetails);

        $savedFormat->season()->save($this->season);
    }
}
