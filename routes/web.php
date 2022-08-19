<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CircuitController;
use App\Http\Controllers\CompleteQualifyingController;
use App\Http\Controllers\CompleteRaceController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EngineController;
use App\Http\Controllers\EngineSeasonController;
use App\Http\Controllers\EntrantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\RacerController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\SeasonSetupCopy\Points;
use App\Http\Controllers\SeasonSetupCopy\Qualifying;
use App\Http\Controllers\SeasonSetupCopy\Races;
use App\Http\Controllers\SeasonSetupCopy\Reliability;
use App\Http\Controllers\SeasonSetupCopy\Teams;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\ShowCopySeasonSettingsPageController;
use App\Http\Controllers\ShowDriverDevelopmentPageController;
use App\Http\Controllers\ShowDriverReliabilityController;
use App\Http\Controllers\ShowDriverStandingsController;
use App\Http\Controllers\ShowEngineDevelopmentPageController;
use App\Http\Controllers\ShowEngineReliabilityController;
use App\Http\Controllers\ShowPointsConfigurationController;
use App\Http\Controllers\ShowQualifyingPageController;
use App\Http\Controllers\ShowQualifyingSettingsPage;
use App\Http\Controllers\ShowRacePageController;
use App\Http\Controllers\ShowRaceResultPageController;
use App\Http\Controllers\ShowRaceWeekendIntroPageController;
use App\Http\Controllers\ShowReliabilityConfigurationController;
use App\Http\Controllers\ShowStartingGridController;
use App\Http\Controllers\ShowTeamDevelopmentPageController;
use App\Http\Controllers\ShowTeamReliabilityController;
use App\Http\Controllers\ShowTeamStandingsController;
use App\Http\Controllers\StartSeasonController;
use App\Http\Controllers\StorePointsConfigurationController;
use App\Http\Controllers\StoreQualifyingResultsController;
use App\Http\Controllers\StoreQualifyingSettingsController;
use App\Http\Controllers\StoreRaceResultsController;
use App\Http\Controllers\StoreReliabilityConfigurationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UniverseController;
use App\Http\Controllers\UpdateDriverRatingsController;
use App\Http\Controllers\UpdateDriverReliabilityController;
use App\Http\Controllers\UpdateEngineRatingsController;
use App\Http\Controllers\UpdateEngineReliabilityController;
use App\Http\Controllers\UpdateTeamRatingsController;
use App\Http\Controllers\UpdateTeamReliabilityController;
use Illuminate\Support\Facades\Route;

Route::get('', [HomeController::class, 'index'])->name('index');

Route::get('/auth/discord/redirect', [AuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/discord/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::resource('circuits', CircuitController::class)->except('destroy');

Route::resource('universes', UniverseController::class)->except('destroy');

Route::group(['prefix' => 'universes/{universe}', 'as' => 'universes.'], function () {
    Route::resource('drivers', DriverController::class)->except('destroy');
    Route::resource('series', SeriesController::class)->except('destroy');
    Route::resource('teams', TeamController::class)->except('destroy');
});

Route::group(['prefix' => 'series/{series}', 'as' => 'series.'], function () {
    Route::resource('engines', EngineController::class)->except('destroy', 'show');
    Route::resource('seasons', SeasonController::class)->except('destroy');
});

Route::group(['prefix' => 'seasons/{season}', 'as' => 'seasons.'], function () {
    Route::put('start', StartSeasonController::class)->name('start');

    Route::get('races/reorder', [RaceController::class, 'reorder'])->name('races.reorder');
    Route::put('races/order', [RaceController::class, 'order'])->name('races.order');
    Route::resource('races', RaceController::class);

    Route::resource('engines', EngineSeasonController::class)->except('destroy', 'show');
    Route::resource('entrants', EntrantController::class)->except('destroy', 'show');
    Route::resource('racers', RacerController::class)->except('destroy', 'create', 'store');
    Route::get('/{entrant}/racer/create', [RacerController::class, 'create'])->name('racers.create');
    Route::post('/{entrant}/racers', [RacerController::class, 'store'])->name('racers.store');

    Route::group(['prefix' => 'settings/copy', 'as' => 'settings.copy.'], function () {
        Route::get('', ShowCopySeasonSettingsPageController::class)->name('index');
        Route::post('teams', Teams::class)->name('teams');
        Route::post('races', Races::class)->name('races');
        Route::post('qualifying', Qualifying::class)->name('qualifying');
        Route::post('points', Points::class)->name('points');
        Route::post('reliability', Reliability::class)->name('reliability');
    });

    Route::group(['prefix' => 'configuration', 'as' => 'configuration.'], function () {
        Route::get('qualifying', ShowQualifyingSettingsPage::class)->name('qualifying');
        Route::post('qualifying', StoreQualifyingSettingsController::class)->name('qualifying.store');

        Route::get('points', ShowPointsConfigurationController::class)->name('points');
        Route::post('points', StorePointsConfigurationController::class)->name('points.store');

        Route::get('reliability', ShowReliabilityConfigurationController::class)->name('reliability');
        Route::post('reliability', StoreReliabilityConfigurationController::class)->name('reliability.store');
    });

    Route::group(['prefix' => 'development', 'as' => 'development.'], function () {
        Route::get('drivers', ShowDriverDevelopmentPageController::class)->name('drivers');
        Route::post('drivers', UpdateDriverRatingsController::class)->name('drivers.store');

        Route::get('teams', ShowTeamDevelopmentPageController::class)->name('teams');
        Route::post('teams', UpdateTeamRatingsController::class)->name('teams.store');

        Route::get('engines', ShowEngineDevelopmentPageController::class)->name('engines');
        Route::post('engines', UpdateEngineRatingsController::class)->name('engines.store');

        Route::group(['prefix' => 'reliability', 'as' => 'reliability.'], function () {
            Route::get('drivers', ShowDriverReliabilityController::class)->name('drivers');
            Route::post('drivers', UpdateDriverReliabilityController::class)->name('drivers.store');

            Route::get('teams', ShowTeamReliabilityController::class)->name('teams');
            Route::post('teams', UpdateTeamReliabilityController::class)->name('teams.store');

            Route::get('engines', ShowEngineReliabilityController::class)->name('engines');
            Route::post('engines', UpdateEngineReliabilityController::class)->name('engines.store');
        });
    });

    Route::group(['prefix' => 'standings', 'as' => 'standings.'], function () {
        Route::get('drivers', ShowDriverStandingsController::class)->name('drivers');
        Route::get('teams', ShowTeamStandingsController::class)->name('teams');
    });
});

Route::group([
    'prefix' => 'races/{race}/weekend',
    'as' => 'weekend.',
    'middleware' => 'season_in_progress',
], function () {
    Route::get('intro', ShowRaceWeekendIntroPageController::class)->name('intro');
    Route::get('qualifying', ShowQualifyingPageController::class)->name('qualifying');
    Route::get('grid', ShowStartingGridController::class)->name('grid');
    Route::get('race', ShowRacePageController::class)->name('race');
    Route::get('results', ShowRaceResultPageController::class)->name('results');

    Route::post('qualifying/results', StoreQualifyingResultsController::class)->name('qualifying.results.store');
    Route::post('qualifying/complete', CompleteQualifyingController::class)->name('qualifying.complete');
    Route::post('race/results', StoreRaceResultsController::class)->name('race.store');
    Route::post('race/complete', CompleteRaceController::class)->name('race.complete');
});
