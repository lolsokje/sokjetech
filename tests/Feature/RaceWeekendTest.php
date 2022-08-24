<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

test('a universe owner can mark a qualifying session as completed', function () {
    [$user, $drivers, $race] = prepareSeason();

    actingAs($user)
        ->post(route('weekend.qualifying.complete', [$race]))
        ->assertRedirect(route('weekend.grid', [$race]));

    assertTrue($race->fresh()->qualifying_completed);
});

test('unauthorized users can not mark a qualifying session as completed', function () {
    [$user, $drivers, $race] = prepareSeason();

    post(route('weekend.qualifying.complete', [$race]))
        ->assertForbidden();

    assertFalse($race->fresh()->qualifying_completed);

    actingAs(User::factory()->create())
        ->post(route('weekend.qualifying.complete', [$race]))
        ->assertForbidden();

    assertFalse($race->fresh()->qualifying_completed);
});

test('a universe owner can mark a race as completed', function () {
    [$user, $drivers, $race] = prepareSeason();

    $this->actingAs($user)
        ->post(route('weekend.race.complete', [$race]))
        ->assertRedirect(route('weekend.results', [$race]));

    $this->assertTrue($race->fresh()->completed);
});

test('unauthorized users cannot mark a race as completed', function () {
    [$user, $drivers, $race] = prepareSeason();

    $this->post(route('weekend.race.complete', [$race]))
        ->assertForbidden();

    $this->assertFalse($race->fresh()->qualifying_completed);

    $this->actingAs(User::factory()->create())
        ->post(route('weekend.race.complete', [$race]))
        ->assertForbidden();

    $this->assertFalse($race->fresh()->completed);
});

it('updates the completed_at column when completing a race', function () {
    [$user, $drivers, $race] = prepareSeason();

    $this->actingAs($user)
        ->post(route('weekend.race.complete', [$race]))
        ->assertRedirect(route('weekend.results', [$race]));

    $this->assertNotNull($race->fresh()->completed_at);
});

test('the starting grid page can only be viewed once qualifying has been completed', function () {
    [$user, $drivers, $race] = prepareSeason();

    get(route('weekend.grid', [$race]))
        ->assertRedirect(route('weekend.qualifying', [$race]));

    $race->update(['qualifying_completed' => true]);

    get(route('weekend.grid', [$race->fresh()]))
        ->assertOk();
});
