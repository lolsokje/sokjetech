<?php

use App\Actions\GetUserAvatar;
use Laravel\Socialite\Two\User;

it('returns the correct avatar', function () {
    $user = new User;
    $avatar = '123456789';
    $user->user['avatar'] = $avatar;
    $user->avatar = "https://cdn.discordapp.com/avatars/123456789/$avatar.png";

    $action = new GetUserAvatar($user);

    $this->assertEquals($user->avatar, $action->handle());
});

it('returns the animated version when compatible', function () {
    $user = new User;
    $avatar = 'a_123456789';
    $user->user['avatar'] = $avatar;
    $user->avatar = "https://cdn.discordapp.com/avatars/123456789/$avatar.png";

    $action = new GetUserAvatar($user);

    $this->assertEquals('https://cdn.discordapp.com/avatars/123456789/a_123456789.gif', $action->handle());
});
