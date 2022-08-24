<?php


use App\Enums\UniverseVisibility;
use App\Models\Race;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

const RACES_PER_SEASON = 4;
const TOTAL_RACES = RACES_PER_SEASON * 4;

it('shows the latest completed races', function () {
    // terribly written test, but it does what it needs to do, go me
    $user = User::factory()->create();
    $userTwo = User::factory()->create();
    $publicSeason = createSeasonForUser($user);

    $privateSeasonOne = createSeasonForUser($user, UniverseVisibility::PRIVATE);
    $authSeason = createSeasonForUser($user, UniverseVisibility::AUTH);

    $privateSeasonTwo = createSeasonForUser($userTwo, UniverseVisibility::PRIVATE);

    foreach ([$publicSeason, $privateSeasonOne, $privateSeasonTwo, $authSeason] as $season) {
        createRacesForSeason($season);
    }

    $this->assertEquals(TOTAL_RACES / 2, Race::query()->where('completed', true)->count());
    $this->assertEquals(TOTAL_RACES / 2, Race::query()->where('completed', false)->count());

    // only races in public universes should be shown
    $this->get(route('index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Index')
            ->has('races', 2));

    // only races in public and auth universes should be shown
    $this->actingAs(User::factory()->create())
        ->get(route('index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Index')
            ->has('races', 4));

    // races in public, auth and private universes should be shown
    $this->actingAs($user)
        ->get(route('index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Index')
            ->has('races', 6));

    $this->actingAs($userTwo)
        ->get(route('index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Index')
            ->has('races', 6));

    $uncompletedPublicRace = $publicSeason->races()->where('completed', false)->first();
    $uncompletedPublicRace->update([
        'completed' => true,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Index')
            ->has('races', 7));
});

function createRacesForSeason(Season $season): void
{
    Race::factory(RACES_PER_SEASON)->for($season)->sequence(
        ['completed' => true, 'completed_at' => fake()->dateTime()],
        ['completed' => false, 'completed_at' => null],
    )->create();
}
