<?php

declare(strict_types=1);

namespace App\Http\Controllers\Season\Development;

use App\Enums\Season\Development\DevelopmentType;
use App\Factories\StoreDevelopmentEntityActionFactory;
use App\Models\Season;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class StoreDevelopmentController
{
    use AuthorizesRequests;

    public function __invoke(
        Request $request,
        Season $season,
        DevelopmentType $type,
        string $component,
    ): RedirectResponse {
        $this->authorize('update', $season->universe);

        if ($season->has_active_race) {
            return to_route('seasons.development.show', [$season, $type, $component])
                ->with(
                    'error',
                    "There's currently an active race, development can't be performed until that race has been completed",
                );
        }

        if (! $season->nextRace()) {
            return to_route('seasons.development.show', [$season, $type, $component])
                ->with('error', 'No next race available for this season');
        }

        $action = StoreDevelopmentEntityActionFactory::create($type);

        DB::transaction(function () use ($action, $request, $season, $component) {
            $action->handle(
                seasonId: $season->id,
                raceId: $season->nextRace()->id,
                entities: $request->get('entities'),
                component: $component,
            );
        });

        return to_route('seasons.development.show', [$season, $type, $component])
            ->with('notice', 'New ratings have been saved');
    }
}
