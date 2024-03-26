<?php

namespace App\Actions\Races\Results;

use App\DataTransferObjects\Race\QualifyingDetails;
use App\Models\Race;

final readonly class UpdateQualifyingDetails
{
    public function handle(
        Race $race,
        QualifyingDetails $details,
    ): void {
        $race->update([
            'qualifying_session' => $details->session,
            'qualifying_run' => $details->run,
        ]);
    }
}
