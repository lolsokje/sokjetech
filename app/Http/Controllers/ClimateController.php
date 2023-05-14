<?php

namespace App\Http\Controllers;

use App\Enums\WeatherConditions;
use App\Models\Climate;
use Inertia\Inertia;

class ClimateController extends Controller
{
    public function index()
    {
        return Inertia::render('Climates/Index', [
            'climates' => Climate::with('conditions')->get(),
            'conditions' => WeatherConditions::casesWithLabels(),
        ]);
    }
}
