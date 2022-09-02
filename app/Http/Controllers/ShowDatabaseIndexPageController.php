<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ShowDatabaseIndexPageController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Database/Index');
    }
}
