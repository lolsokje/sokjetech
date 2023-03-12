<?php

namespace App\Support\Http;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomJsonResource extends JsonResource
{
    /**
     * Immediately returns the array representation of the API resource
     *
     * @param Model $resource
     * @return array
     */
    public static function array(Model $resource): array
    {
        return (new static($resource))->toArray(request());
    }
}
