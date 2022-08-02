<?php

use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

test('a universe owner can view the point configuration page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->get(route('seasons.configuration.points', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Seasons/Configuration/Points')
                ->has('season')
        );
});

test('an unauthenticated user cannot view a point configuration page', function () {
    $season = Season::factory()->create();

    get(route('seasons.configuration.points', [$season]))
        ->assertForbidden();
});

test('an authenticated user cannot view another users points configuration page', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    actingAs($user)
        ->get(route('seasons.configuration.points', [$season]))
        ->assertForbidden();
});

test('a universe owner can store a point system', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect(route('seasons.configuration.points', [$season]));

    assertDatabaseCount('point_systems', 1);
    assertDatabaseCount('point_distributions', 10);
    assertNotNull($season->pointSystem);
    assertCount(10, $season->pointDistribution);
});

test('an unauthenticated user cannot store a point system', function () {
    $season = Season::factory()->create();

    post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertForbidden();

    assertDatabaseCount('point_systems', 0);
    assertDatabaseCount('point_distributions', 0);
    assertNull($season->pointSystem);
    assertCount(0, $season->pointDistribution);
});

test('an authenticated user cannot store a point system in another users universe', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertForbidden();

    assertDatabaseCount('point_systems', 0);
    assertDatabaseCount('point_distributions', 0);
    assertNull($season->pointSystem);
    assertCount(0, $season->pointDistribution);
});

test('the existing point system will be removed when updating', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $system = PointSystem::factory()->for($season)->create();
    PointDistribution::factory(10)->for($system)->create();

    actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect(route('seasons.configuration.points', [$season]));

    assertDatabaseCount('point_systems', 1);
    assertDatabaseCount('point_distributions', 10);
    assertNotNull($season->pointSystem);
    assertCount(10, $season->pointDistribution);
});

test('a point system cannot be saved when a season has started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect();
});

function getPointSystemData(?array $override = []): array
{
    return array_merge([
        'points' => [
            ['position' => 1, 'points' => 10],
            ['position' => 2, 'points' => 9],
            ['position' => 3, 'points' => 8],
            ['position' => 4, 'points' => 7],
            ['position' => 5, 'points' => 6],
            ['position' => 6, 'points' => 5],
            ['position' => 7, 'points' => 4],
            ['position' => 8, 'points' => 3],
            ['position' => 9, 'points' => 2],
            ['position' => 10, 'points' => 1],
        ],
        'pole_position_point_awarded' => false,
        'fastest_lap_point_awarded' => false,
    ], $override);
}
