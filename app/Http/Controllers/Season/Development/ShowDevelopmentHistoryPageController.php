<?php

declare(strict_types=1);

namespace App\Http\Controllers\Season\Development;

use App\Enums\Season\Development\DevelopmentType;
use App\Factories\DevelopmentHistoryActionFactory;
use App\Http\Resources\Season\Development\DevelopmentHistoryRaceResource;
use App\Models\Season;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ShowDevelopmentHistoryPageController
{
    use AuthorizesRequests;

    public function __invoke(
        Season $season,
        DevelopmentType $type = DevelopmentType::DRIVER,
        string $component = 'rating',
    ): Response {
        $this->authorize('view', $season->universe);

        $races = $season->races()
            ->with('circuit:id,country')
            ->orderBy('order')
            ->get();

        $action = DevelopmentHistoryActionFactory::create($type);

        return Inertia::render('Development/History', [
            'season' => $season,
            'results' => $action->handle($season, $races, $component),
            'races' => DevelopmentHistoryRaceResource::collection($races),
            'type' => $type->value,
            'component' => $component,
        ]);
    }
}
