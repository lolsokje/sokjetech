<?php

namespace App\Http\Resources\Race\Results;

use App\ValueObjects\Race\Results\DriverDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DriverDetails */
class DriverDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'number' => $this->number,
        ];
    }
}
