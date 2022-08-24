<?php

namespace App\Http\Controllers;

use App\Actions\GetRacesForIndexPage;
use App\Http\Resources\IndexPageRaceResource;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $races = (new GetRacesForIndexPage())->handle();

        return Inertia::render('Index', [
            'races' => IndexPageRaceResource::collection($races)->toArray(request()),
        ]);
    }
}
