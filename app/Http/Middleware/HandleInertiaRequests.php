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
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  Request  $request
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function share(Request $request): array
    {
        $universe = $this->getUniverseFromRequest($request);

        $props = array_merge(parent::share($request), [
            'auth.user' => fn() => $request->user()
                ? $request->user()->only('username', 'avatar')
                : null,
            'notice' => $request->session()->get('notice'),
        ]);

        if ($universe) {
            $props['can']['edit'] = Gate::check('owns-universe', $universe);
        }

        return $props;
    }

    private function getUniverseFromRequest(Request $request): ?Universe
    {
        $route = $request->route();

        if ($route->hasParameter('universe')) {
            return new Universe($route->parameter('universe')->toArray());
        } elseif ($route->hasParameter('series')) {
            $series = $route->parameter('series');
            return $series->universe;
        } elseif ($route->hasParameter('season')) {
            $season = $route->parameter('season');
            return $season->universe;
        }
        return null;
    }
}
