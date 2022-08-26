<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ShowTutorialPageController extends Controller
{
    public function __invoke(?string $page = 'universes'): Response
    {
        $page = $this->parsePage($page);

        return Inertia::render("Tutorials/$page");
    }

    private function parsePage(?string $page): string
    {
        $parts = explode('/', $page);

        return implode('/', array_map(fn (string $part) => ucfirst($part), $parts));
    }
}
