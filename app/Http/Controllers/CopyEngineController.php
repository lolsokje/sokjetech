<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyEngineRequest;
use App\Models\Engine;
use App\Models\Series;
use Illuminate\Http\RedirectResponse;

class CopyEngineController extends Controller
{
    public function __invoke(CopyEngineRequest $request, Engine $engine): RedirectResponse
    {
        $series = Series::find($request->validated('series_id'));
        $this->authorize('update', $series->universe);

        $newEngine = $engine->replicate();
        $newEngine->series()->associate($series);
        $newEngine->save();

        return to_route('database.engines.index')
            ->with('notice', 'Engine copied');
    }
}
