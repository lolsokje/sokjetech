<?php

use App\Models\QualifyingFormats\SingleSession;
use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\UnauthorizedException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

test('unauthorized users cannot copy qualifying formats', function () {
    $season = Season::factory()->create();
    prepareQualifyingFormat($season);
    $newSeason = Season::factory()->create();

    post(route('seasons.settings.copy.qualifying', [$newSeason]), [
        'season_id' => $season->id,
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertForbidden();

    assertNull($newSeason->fresh()->qualifyingFormat);
});

it('requires a source season ID when copying a qualifying format', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    prepareQualifyingFormat($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs a qualifying format before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertJson(['error' => 'No qualifying format configured for the selected season']);
});

it('clears the existing qualifying format from the new season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareQualifyingFormat($season);

    $newSeason = createSeasonForUser($user);

    assertDatabaseCount('single_sessions', 1);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertDatabaseCount('single_sessions', 2);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertDatabaseCount('single_sessions', 2);
});

test('a universe owner can copy a single session qualifying format', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $oldFormat = prepareQualifyingFormat($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();
    $oldSeason = $season->fresh();

    assertNotNull($newSeason->qualifyingFormat);
    assertNotNull($oldSeason->qualifyingFormat);

    $newFormat = $newSeason->qualifyingFormat;

    assertEquals($oldFormat->runs_per_session, $newFormat->runs_per_session);
    assertEquals($oldFormat->min_rng, $newFormat->min_rng);
    assertEquals($oldFormat->max_rng, $newFormat->max_rng);
});

test('a universe owner can copy a three session elimination qualifying session', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $oldFormat = prepareQualifyingFormat($season, ThreeSessionElimination::class);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.qualifying', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();
    $oldSeason = $season->fresh();

    assertNotNull($newSeason->qualifyingFormat);
    assertNotNull($oldSeason->qualifyingFormat);

    $newFormat = $newSeason->qualifyingFormat;

    assertEquals($oldFormat->q2_driver_count, $newFormat->q2_driver_count);
    assertEquals($oldFormat->q3_driver_count, $newFormat->q3_driver_count);
    assertEquals($oldFormat->runs_per_session, $newFormat->runs_per_session);
    assertEquals($oldFormat->min_rng, $newFormat->min_rng);
    assertEquals($oldFormat->max_rng, $newFormat->max_rng);
});

function prepareQualifyingFormat(Season $season, ?string $format = SingleSession::class): Model
{
    $oldFormat = $format::factory()->create();
    $oldFormat->season()->save($season);

    return $oldFormat;
}
