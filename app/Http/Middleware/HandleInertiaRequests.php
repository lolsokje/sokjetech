<?php

namespace App\Http\Middleware;

use App\Actions\GetUniverseFromRequest;
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
        $universe = (new GetUniverseFromRequest($request))->handle();

        return array_merge(parent::share($request), [
            'auth.user' => fn () => $request->user()
                ? $request->user()->only('username', 'avatar')
                : null,
            'flash' => [
                'notice' => fn () => $request->session()->get('notice'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'can' => [
                'edit' => Gate::check('owns-universe', $universe),
            ],
        ]);
    }
}
