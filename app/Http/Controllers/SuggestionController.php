<?php

namespace App\Http\Controllers;

use App\Actions\GetSuggestions;
use App\Http\Requests\StoreSuggestionRequest;
use App\Http\Requests\SuggestionFilterRequest;
use App\Http\Requests\UpdateSuggestionRequest;
use App\Http\Resources\SuggestionResource;
use App\Models\Suggestion;
use Auth;
use Inertia\Inertia;

class SuggestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin')->only('update');
    }

    public function index(SuggestionFilterRequest $request)
    {
        $suggestions = (new GetSuggestions($request))->handle();

        return Inertia::render('Suggestions/Index', [
            'suggestions' => SuggestionResource::collection($suggestions)->toArray($request),
            'links' => $suggestions->linkCollection(),
            'filters' => $request->validated(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Suggestions/Create');
    }

    public function store(StoreSuggestionRequest $request)
    {
        Auth::user()->suggestions()->create($request->validated());

        return to_route('suggestions.index')
            ->with('notice', 'Suggestion submitted');
    }

    public function edit(Suggestion $suggestion)
    {
        return Inertia::render('Suggestions/Edit', [
            'suggestion' => $suggestion,
            'can' => [
                'edit' => auth()->user()?->is_admin,
            ],
        ]);
    }

    public function update(UpdateSuggestionRequest $request, Suggestion $suggestion)
    {
        $suggestion->update($request->validated());

        return to_route('suggestions.edit', $suggestion)
            ->with('notice', 'Suggestion updated');
    }
}
