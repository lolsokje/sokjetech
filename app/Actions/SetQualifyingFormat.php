<?php

namespace App\Actions;

use App\Enums\QualifyingFormat;
use App\Http\Requests\QualifyingConfigurationRequest;
use App\Models\Season;

class SetQualifyingFormat implements Action
{
    public function __construct(protected Season $season, protected QualifyingConfigurationRequest $request)
    {
    }

    public function handle(): void
    {
        $validated = $this->request->validated();

        $format = QualifyingFormat::tryFrom($validated['selected_format']);

        $this->season->qualifyingFormat?->delete();

        $class = $format->modelFullyQualifiedClassName();

        $savedFormat = $class::create($this->request->validated(['format_details']));

        $savedFormat->season()->save($this->season);
    }
}
