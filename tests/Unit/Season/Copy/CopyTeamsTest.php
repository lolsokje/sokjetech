<?php

use App\Actions\Season\Copy\CopyTeams;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Entrant;
use App\Models\User;

it('copies teams', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeams($season, $newSeason))->handle();

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', ['season_id' => $season->id]);
    $this->assertDatabaseHas('entrants', ['season_id' => $newSeason->id]);
});

test('teams must exist in the old season before copying', function () {
    [$season, $newSeason] = prepareTeams();

    $season->entrants()->delete();

    $this->assertDatabaseCount('entrants', 0);

    (new CopyTeams($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('does not copy ratings when not requested to', function () {
    [$season, $newSeason] = prepareTeams();

    (new CopyTeams($season, $newSeason))->handle();

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

    (new CopyTeams($season, $newSeason))->handle(copyRatings: true);

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

    (new CopyTeams($season, $newSeason))->handle(copyRatings: true);
    (new CopyTeams($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('entrants', 6);

    $this->assertDatabaseHas('entrants', ['season_id' => $season->id]);
    $this->assertDatabaseHas('entrants', ['season_id' => $newSeason->id]);
});

function prepareTeams(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Entrant::factory(3)->for($season)->create();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
