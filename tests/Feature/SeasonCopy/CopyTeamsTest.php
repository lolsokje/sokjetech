<?php

use App\Models\Entrant;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

const TEAM_COUNT = 2;

test('unauthorized users cannot copy teams', function () {
    $season = Season::factory()->create();

    post(route('seasons.settings.copy.teams', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.teams', [$season]))
        ->assertForbidden();
});

it('requires a source season id when copying teams', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    prepareSeasonTeams($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs teams before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertJson(['error' => 'No entrants added to the selected season']);
});

it('clears existing teams before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonTeams($season);

    $newSeason = createSeasonForUser($user);
    prepareSeasonTeams($newSeason);

    assertCount(TEAM_COUNT, $newSeason->entrants);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    assertCount(TEAM_COUNT, $newSeason->entrants);
});

test('a universe owner can copy teams', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonTeams($season);

    assertCount(TEAM_COUNT, $season->entrants);

    $newSeason = createSeasonForUser($user);

    assertCount(0, $newSeason->entrants);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $season = $season->fresh();
    $newSeason = $newSeason->fresh();

    assertCount(TEAM_COUNT, $season->entrants);
    assertCount(TEAM_COUNT, $newSeason->entrants);
});

it('does not copy team ratings when not instructed to', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonTeams($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', $newSeason), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    foreach ($newSeason->entrants as $entrant) {
        assertEquals(0, $entrant->rating);
        assertEquals(0, $entrant->reliability);
    }
});

it('copies team ratings when instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonTeams($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', $newSeason), [
            'season_id' => $season->id,
            'copy_ratings' => true,
        ])
        ->assertCreated();

    foreach ($newSeason->entrants as $newEntrant) {
        // Since each engine is created with its own base team, we can use that to fetch the old team from the old season
        $oldEntrant = Entrant::where('season_id', $season->id)->where('team_id', $newEntrant->team_id)->first();
        assertEquals($oldEntrant->rating, $newEntrant->rating);
        assertEquals($oldEntrant->reliability, $newEntrant->reliability);
    }
});

function prepareSeasonTeams(Season $season): void
{
    Entrant::factory(TEAM_COUNT)->for($season)->create();
}
