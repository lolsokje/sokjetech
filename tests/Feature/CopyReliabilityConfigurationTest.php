<?php

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\ReliabilityConfiguration;
use App\Models\ReliabilityReason;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

const REASON_COUNT = 3;

test('unauthorized users cannot copy reliability configurations', function () {
    $season = Season::factory()->create();
    prepareReliabilityConfiguration($season);
    $newSeason = Season::factory()->create();

    post(route('seasons.settings.copy.reliability', [$newSeason]), [
        'season_id' => $season->id,
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.reliability', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertForbidden();

    assertNull($newSeason->fresh()->reliabilityConfiguration);
    assertCount(0, $newSeason->reliabilityReasons()->get());
});

it('requires a source season ID when copying a reliability configuration', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.reliability', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    prepareReliabilityConfiguration($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.reliability', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs a reliability configuration before copying', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.reliability', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(InvalidSeasonRequirements::class);

it('clears an existing reliability configuration before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareReliabilityConfiguration($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.reliability', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertDatabaseCount('reliability_configurations', 2);
    assertDatabaseCount('reliability_reasons', REASON_COUNT * 2);

    post(route('seasons.settings.copy.reliability', [$newSeason]), [
        'season_id' => $season->id,
    ])
        ->assertCreated();

    assertDatabaseCount('reliability_configurations', 2);
    assertDatabaseCount('reliability_reasons', REASON_COUNT * 2);
});

test('a universe owner can copy a reliability configuration', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareReliabilityConfiguration($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.reliability', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    assertNotNull($newSeason->reliabilityConfiguration);
    assertCount(REASON_COUNT, $newSeason->reliabilityReasons);
});

function prepareReliabilityConfiguration(Season $season): void
{
    ReliabilityConfiguration::factory()->for($season)->create();
    ReliabilityReason::factory(REASON_COUNT)->for($season)->create();
}
