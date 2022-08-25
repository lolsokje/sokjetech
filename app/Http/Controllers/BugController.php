<?php

namespace App\Http\Controllers;

use App\Actions\GetBugs;
use App\Http\Requests\BugFilterRequest;
use App\Http\Requests\BugStoreRequest;
use App\Http\Requests\BugUpdateRequest;
use App\Http\Resources\BugResource;
use App\Models\Bug;
use Inertia\Inertia;
use Inertia\Response;

class BugController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin')->only('update');
    }

    public function index(BugFilterRequest $request): Response
    {
        $bugs = (new GetBugs($request))->handle()->withQueryString();

        return Inertia::render('Bugs/Index', [
            'links' => $bugs->linkCollection(),
            'bugs' => BugResource::collection($bugs)->toArray(request()),
            'filters' => $request->validated(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Bugs/Create');
    }

    public function store(BugStoreRequest $request)
    {
        $request->user()->bugs()->create(array_merge(
            $request->validated(),
            ['app_version' => config('app.version')],
        ));

        return to_route('bugs.index')
            ->with('notice', 'Your bug has been reported');
    }

    public function edit(Bug $bug): Response
    {
        return Inertia::render('Bugs/Edit', [
            'bug' => $bug,
            'can' => [
                'edit' => auth()->user()?->is_admin,
            ],
        ]);
    }

    public function update(BugUpdateRequest $request, Bug $bug)
    {
        $bug->update($request->validated());

        return to_route('bugs.edit', $bug)
            ->with('notice', 'Bug updated');
    }
}
