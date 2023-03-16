<?php

use App\Models\EngineSeason;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

const ENGINE_COUNT = 2;

test('unauthorized users cannot copy engines', function () {
    $season = Season::factory()->create();

    $this->post(route('seasons.settings.copy.engines', [$season]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.engines', [$season]))
        ->assertForbidden();
});

it('requires a source season id when copying engines', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs engines before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertJson(['error' => 'No engines added to the selected season']);
});

it('clears existing engines before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonEngines($season);

    $newSeason = createSeasonForUser($user);
    prepareSeasonEngines($newSeason);

    $this->assertCount(ENGINE_COUNT, $newSeason->engines);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    $this->assertCount(ENGINE_COUNT, $newSeason->engines);
});

test('a universe owner can copy engines', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonEngines($season);

    $this->assertCount(ENGINE_COUNT, $season->engines);

    $newSeason = createSeasonForUser($user);

    $this->assertCount(0, $newSeason->engines);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $season = $season->fresh();
    $newSeason = $newSeason->fresh();

    $this->assertCount(ENGINE_COUNT, $season->engines);
    $this->assertCount(ENGINE_COUNT, $newSeason->engines);
});

it('does not copy engine ratings when not instructed to', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonEngines($season);

    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', $newSeason), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    foreach ($newSeason->engines as $engine) {
        $this->assertEquals(0, $engine->rating);
        $this->assertEquals(0, $engine->reliability);
    }
});

it('copies engine ratings when instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonEngines($season);

    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.engines', $newSeason), [
            'season_id' => $season->id,
            'copy_ratings' => true,
        ])
        ->assertCreated();

    foreach ($newSeason->engines as $newEngine) {
        $oldEngine = EngineSeason::where('season_id', $season->id)
            ->where('base_engine_id', $newEngine->base_engine_id)
            ->first();
        $this->assertEquals($oldEngine->rating, $newEngine->rating);
        $this->assertEquals($oldEngine->reliability, $newEngine->reliability);
    }
});

function prepareSeasonEngines(Season $season): void
{
    EngineSeason::factory(ENGINE_COUNT)->for($season)->create();
}
