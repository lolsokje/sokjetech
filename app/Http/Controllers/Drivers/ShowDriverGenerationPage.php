<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Universe;
use Inertia\Inertia;
use Inertia\Response;
use LilPecky\RandomPersonGenerator\Languages;

class ShowDriverGenerationPage extends Controller
{
    public function __invoke(Universe $universe): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Drivers/Generate', [
            'universe' => $universe,
            'languages' => Languages::getLanguages(),
        ]);
    }
}
