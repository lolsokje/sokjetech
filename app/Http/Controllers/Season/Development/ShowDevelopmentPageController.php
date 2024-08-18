<?php

declare(strict_types=1);

namespace App\Http\Controllers\Season\Development;

use App\Enums\Season\Development\DevelopmentType;
use App\Factories\DevelopmentEntityActionFactory;
use App\Models\Season;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

final class ShowDevelopmentPageController
{
    use AuthorizesRequests;

    public function __invoke(
        Season $season,
        DevelopmentType $type = DevelopmentType::DRIVER,
        string $component = 'rating',
    ): Response {
        $this->authorize('update', $season->universe);

        $action = DevelopmentEntityActionFactory::create($type);

        $entities = $action->handle($season, $component);

        return Inertia::render('Development/Show', [
            'season' => $season,
            'entities' => $entities,
            'type' => $type->value,
            'component' => $component,
            'has_active_race' => $season->has_active_race,
            'has_next_race' => $season->nextRace() !== null,
        ]);
    }
}
