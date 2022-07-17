<?php

namespace App\Actions;

use App\Models\Universe;
use Illuminate\Http\Request;

class GetUniverseFromRequest
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(): ?Universe
    {
        $parameters = $this->request->route()->parameters();
        $parameterKeys = array_keys($parameters);

        if (in_array('universe', $parameterKeys)) {
            return new Universe($parameters['universe']->toArray());
        }

        if (in_array('series', $parameterKeys)) {
            return $parameters['series']->universe;
        }

        if (in_array('season', $parameterKeys)) {
            return $parameters['season']->universe;
        }

        if (in_array('race', $parameterKeys)) {
            return $parameters['race']->season->universe;
        }
        return null;
    }
}
