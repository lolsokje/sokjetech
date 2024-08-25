<?php

use App\Models\User;

it('redirects to discord', function () {
    $this->get(route('auth.redirect'))
        ->assertRedirect();
});

it('creates a new user after logging in through discord', function () {
    setupSocialiteMocking();

    $this->get(route('auth.callback'))
        ->assertRedirectToRoute('index');

    $this->assertCount(1, User::query()->get());

    $user = User::query()->first();
    $this->assertAuthenticatedAs($user);
    $this->assertEquals(0, $user->is_admin);
});

it('correctly determines admin users', function () {
    setupSocialiteMocking(id: '136818922006511616');

    $this->get(route('auth.callback'))
        ->assertRedirectToRoute('index');

    $this->assertCount(1, User::query()->get());

    $user = User::query()->first();
    $this->assertAuthenticatedAs($user);
    $this->assertEquals(1, $user->is_admin);
});

it('does not grant access to unauthorised users on the staging environment', function () {
    $this->withoutExceptionHandling();
    $this->expectExceptionMessage('No access to the staging environment');

    Config::set('app.env', 'staging');

    setupSocialiteMocking();

    $this->get(route('auth.callback'));

    $this->assertCount(0, User::query()->get());
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('auth.logout'))
        ->assertRedirectToRoute('index');

    $this->assertGuest();
});

it('redirects to the referrer page when set', function () {
    setupSocialiteMocking();

    $this->from(route('universes.index'))
        ->get(route('auth.redirect'));

    $this->get(route('auth.callback'))
        ->assertRedirectToRoute('universes.index');
});

function setupSocialiteMocking(
    string $id = '123456789',
    string $username = 'username',
    string $avatar = '123456789',
): void {
    $mockedUser = Mockery::mock(Laravel\Socialite\Two\User::class);
    $mockedUser->user['avatar'] = $avatar;

    $mockedUser->shouldReceive('getId')->andReturn($id);
    $mockedUser->shouldReceive('getName')->andReturn($username);
    $mockedUser->shouldReceive('getAvatar')->andReturn($avatar);

    $mockedProvider = Mockery::mock(Laravel\Socialite\Contracts\Provider::class);
    $mockedProvider->shouldReceive('user')->andReturn($mockedUser);

    Socialite::shouldReceive('driver')->andReturn($mockedProvider);
}
