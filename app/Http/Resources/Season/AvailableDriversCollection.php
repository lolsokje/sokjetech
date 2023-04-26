<?php

namespace App\Http\Resources\Season;

use App\Models\Driver;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see Driver */
class AvailableDriversCollection extends ResourceCollection
{
    public function __construct($resource, protected readonly Season $season)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return $this->collection->map(function (Driver $driver) {
            $age = $driver->age($this->season);

            return [
                'id' => $driver->id,
                'full_name' => $driver->full_name,
                'age' => $age,
                'full_name_with_age' => "$driver->full_name ($age)",
            ];
        })->toArray();
    }
}
