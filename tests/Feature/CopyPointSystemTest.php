<?php

use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

const POINTS_COUNT = 3;

test('unauthorised users cannot copy point systems', function () {
    $season = Season::factory()->create();
    preparePointsSystem($season);

    $newSeason = Season::factory()->create();

    post(route('seasons.settings.copy.points', [$newSeason]), [
        'season_id' => $season->id,
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.points', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertForbidden();

    assertNull($newSeason->fresh()->pointSystem);
    assertCount(0, $newSeason->fresh()->pointDistribution);
});

it('requires a source season ID when copying a point system', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.points', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    preparePointsSystem($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.points', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs a point system before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.points', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertJson(['error' => 'No point system added to the selected season']);

    $newSeason = $newSeason->fresh();

    assertNull($newSeason->pointSystem);
    assertCount(0, $newSeason->pointDistribution);
});

it('clears an existing point system before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    preparePointsSystem($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.points', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertCount(2, PointSystem::all());
    assertCount(POINTS_COUNT, $newSeason->fresh()->pointDistribution);
});

test('a universe owner can copy a points system', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    preparePointsSystem($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.points', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    assertNotNull($newSeason->pointSystem);
    assertCount(POINTS_COUNT, $newSeason->pointDistribution);
});

function preparePointsSystem(Season $season): void
{
    $system = PointSystem::factory()->for($season)->create();
    PointDistribution::factory(POINTS_COUNT)->for($system)->create();
}
