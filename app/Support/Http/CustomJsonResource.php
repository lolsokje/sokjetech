<?php

namespace App\Support\Http;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomJsonResource extends JsonResource
{
    /**
     * Immediately returns the array representation of the API resource
     */
    public static function array(Model|Collection $resource): array
    {
        return (new static($resource))->toArray(request());
    }

    /**
     * Immediately returns the array representation of the API resource collection
     */
    public static function collectionArray(Collection $resource): array
    {
        return self::collection($resource)->toArray(request());
    }
}
