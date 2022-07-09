<?php

namespace App\Http\Middleware;

use App\Models\Universe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param Request $request
     *
     * @return array
     */
    public function share(Request $request): array
    {
        $universe = $this->getUniverseFromRequest($request);

        return array_merge(parent::share($request), [
            'auth.user' => fn () => $request->user()
                ? $request->user()->only('username', 'avatar')
                : null,
            'flash' => [
                'notice' => fn () => $request->session()->get('notice'),
            ],
            'can' => [
                'edit' => Gate::check('owns-universe', $universe),
            ],
        ]);
    }

    private function getUniverseFromRequest(Request $request): ?Universe
    {
        $parameters = $request->route()->parameters();
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
        return null;
    }
}
