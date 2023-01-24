<?php

namespace App\Http\Controllers;

use App\Actions\GetSharedTeams;
use App\Http\Requests\TeamFilterRequest;
use App\Http\Resources\TeamResource;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamDatabaseIndexPageController extends Controller
{
    public function __invoke(TeamFilterRequest $request): Response
    {
        $teams = (new GetSharedTeams($request))->handle();

        return Inertia::render('Database/Teams/Index', [
            'teams' => TeamResource::collection($teams)->toArray(request()),
            'links' => $teams->linkCollection(),
            'filters' => $request->validated(),
            'universes' => auth()->user()->universes()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
