<?php

use App\Actions\Season\DeleteSeason;
use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\Actions\Season\Standings\CalculateTeamChampionshipStandingsAction;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\QualifyingFormats\SingleSession;
use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\ReliabilityConfiguration;
use App\Models\ReliabilityReason;
use App\Models\Season;

it('deletes a season and all dependencies', function () {
    $season = Season::factory()->create();

    $engine = EngineSeason::factory()->for($season)->create();
    $entrant = Entrant::factory()->for($season)->create(['engine_id' => $engine->id]);
    $racer = Racer::factory()->for($season)->for($entrant)->create();

    ReliabilityReason::factory()->for($season)->create();
    ReliabilityConfiguration::factory()->for($season)->create();

    $format = SingleSession::factory()->create();
    $season->qualifyingFormat()->associate($format)->save();

    $system = PointSystem::factory()->for($season)->create();
    PointDistribution::factory()->for($system)->create();

    $race = Race::factory()->for($season)->withStints()->create();

    QualifyingResult::factory()
        ->for($race)
        ->for($season)
        ->for($entrant)
        ->for($racer)
        ->create();

    RaceResult::factory()
        ->for($race)
        ->for($season)
        ->for($entrant)
        ->for($racer)
        ->create();

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season)
    ))->handle();

    (new CalculateChampionshipStandingsJob(
        new CalculateTeamChampionshipStandingsAction($season)
    ))->handle();

    (new DeleteSeason($season))->handle();

    $this->assertDatabaseCount('seasons', 0);
    $this->assertDatabaseCount('engine_seasons', 0);
    $this->assertDatabaseCount('entrants', 0);
    $this->assertDatabaseCount('racers', 0);
    $this->assertDatabaseCount('races', 0);
    $this->assertDatabaseCount('stints', 0);
    $this->assertDatabaseCount('reliability_reasons', 0);
    $this->assertDatabaseCount('reliability_configurations', 0);
    $this->assertDatabaseCount('single_sessions', 0);
    $this->assertDatabaseCount('point_systems', 0);
    $this->assertDatabaseCount('point_distributions', 0);
    $this->assertDatabaseCount('qualifying_results', 0);
    $this->assertDatabaseCount('race_results', 0);
    $this->assertDatabaseCount('driver_championship_standings', 0);
    $this->assertDatabaseCount('team_championship_standings', 0);
});
