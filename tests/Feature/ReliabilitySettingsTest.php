<?php

use App\Models\Season;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

test('a universe owner can view the reliability configuration page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->get(route('seasons.configuration.reliability', [$season]))
        ->assertOk();
});

test('unauthorised users cannot view the reliability configuration page', function () {
    $season = Season::factory()->create();

    get(route('seasons.configuration.reliability', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->get(route('seasons.configuration.reliability', [$season]))
        ->assertForbidden();
});

test('a universe owner can store min and max reliability RNG', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertNotNull($season->reliabilityConfiguration);
    assertEquals(5, $season->reliabilityConfiguration->min_rng);
    assertEquals(10, $season->reliabilityConfiguration->max_rng);
});

test('the min and max RNG values are required', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]))
        ->assertInvalid(['min_rng' => 'required', 'max_rng' => 'required']);

    assertNull($season->reliabilityConfiguration);
});

it('only stores one reliability configuration for a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertNotNull($season->reliabilityConfiguration);
    assertEquals(5, $season->reliabilityConfiguration->min_rng);
    assertEquals(10, $season->reliabilityConfiguration->max_rng);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody(['min_rng' => 20, 'max_rng' => 30]))
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertDatabaseCount('reliability_configurations', 1);
    assertEquals(20, $season->fresh()->reliabilityConfiguration->min_rng);
    assertEquals(30, $season->fresh()->reliabilityConfiguration->max_rng);
});

test('unauthorised users cannot store reliability configurations', function () {
    $season = Season::factory()->create();

    post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertForbidden();

    assertNull($season->reliabilityConfiguration);
});

test('a universe owner can store reliability reasons', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertCount(6, $season->reliabilityReasons);
    assertDatabaseCount('reliability_reasons', 6);
});

it('clears existing reasons before storing new ones', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertCount(6, $season->reliabilityReasons);
    assertDatabaseCount('reliability_reasons', 6);
});

test('reasons must be delimited by a newline character', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $body = getRequestBody([
        'reasons' => [
            'engine' => "reason 1 reason 2",
            'team' => "reason 3 reason 4",
            'driver' => "reason 4 reason 5",
        ],
    ]);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), $body)
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertCount(3, $season->reliabilityReasons);
    assertDatabaseCount('reliability_reasons', 3);

    post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    assertCount(6, $season->reliabilityReasons()->get());
    assertDatabaseCount('reliability_reasons', 6);
});

test('the reason types must exist', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody(['reasons' => ['test' => 'reasons']]))
        ->assertInvalid(['reason_keys' => 'in']);

    assertCount(0, $season->reliabilityReasons);
    assertDatabaseCount('reliability_reasons', 0);
});

function getRequestBody(?array $override = []): array
{
    $data = [
        'min_rng' => 5,
        'max_rng' => 10,
        'reasons' => [
            'engine' => "reason 5" . PHP_EOL . "reason 6",
            'team' => "reason 3" . PHP_EOL . "reason 4",
            'driver' => "reason 1" . PHP_EOL . "reason 2",
        ],
    ];

    return array_merge($data, $override);
}
