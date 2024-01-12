<?php

namespace App\Actions\Races\Results;

use App\Models\Race;

final readonly class UpdateQualifyingDetails
{
    public function handle(
        Race $race,
        array $details,
    ): void {
        $updateArray = ['qualifying_details' => $details];

        if (! $race->qualifying_started) {
            $updateArray['qualifying_started'] = true;
        }

        $race->update($updateArray);
    }
}
