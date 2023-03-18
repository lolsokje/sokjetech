<?php

use App\Actions\Season\Copy\CopyTeamsAndEngines;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\User;

it('copies teams', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeamsAndEngines($season, $newSeason))->handle();

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', ['season_id' => $season->id]);
    $this->assertDatabaseHas('entrants', ['season_id' => $newSeason->id]);
});

it('copies engines', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeamsAndEngines($season, $newSeason))->handle();

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertCount(3, $season->engines);
    $this->assertCount(3, $newSeason->engines);

    $this->assertNotEquals($season->entrants->first()->engine_id, $newSeason->entrants->first()->engine_id);
});

it('does not duplicate engines', function () {
    [$season, $newSeason] = prepareTeams();

    Entrant::factory()->for($season)->create([
        'engine_id' => $season->engines->first()->id,
    ]);

    (new CopyTeamsAndEngines($season, $newSeason))->handle();

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertCount(3, $season->engines);
    $this->assertCount(3, $newSeason->engines);

    $this->assertNotEquals($season->entrants->first()->engine_id, $newSeason->entrants->first()->engine_id);
});

test('teams must exist in the old season before copying', function () {
    [$season, $newSeason] = prepareTeams();

    $season->entrants()->delete();

    $this->assertDatabaseCount('entrants', 0);

    (new CopyTeamsAndEngines($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('does not copy ratings when not requested to', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeamsAndEngines($season, $newSeason))->handle();

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', [
        'season_id' => $season->id,
        'rating' => $season->entrants->first()->rating,
        'reliability' => $season->entrants->first()->reliability,
    ]);

    $this->assertDatabaseHas('entrants', [
        'season_id' => $newSeason->id,
        'rating' => 0,
        'reliability' => 0,
    ]);
});

it('copies ratings when requested to', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeamsAndEngines($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', [
        'season_id' => $season->id,
        'rating' => $season->entrants->first()->rating,
        'reliability' => $season->entrants->first()->reliability,
    ]);

    $this->assertDatabaseHas('entrants', [
        'season_id' => $newSeason->id,
        'rating' => $season->entrants->first()->rating,
        'reliability' => $season->entrants->first()->reliability,
    ]);
});

it('removes existing teams from the new season before creating new teams', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeamsAndEngines($season, $newSeason))->handle(copyRatings: true);
    (new CopyTeamsAndEngines($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', ['season_id' => $season->id]);
    $this->assertDatabaseHas('entrants', ['season_id' => $newSeason->id]);
});

function prepareTeams(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $engines = EngineSeason::factory(3)->for($season)->create();

    foreach ($engines as $engine) {
        Entrant::factory()->for($season)->create([
            'engine_id' => $engine->id,
        ]);
    }

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
