<?php

namespace App\Actions;

use Laravel\Socialite\Contracts\User;

class GetUserAvatar
{
    public function __construct(protected User $user)
    {
    }

    public function handle(): ?string
    {
        // getAvatar() will always return a string, even if no avatar exists. The user array contains null if no avatar
        // is set by the user, so that value is checked before assigning the variable.
        $userAvatar = $this->user->user['avatar'];
        $avatar = $userAvatar ? $this->user->getAvatar() : null;

        if ($avatar && $this->isGifCompatible($userAvatar)) {
            $avatar = str_replace('.png', '.gif', $avatar);
        }
        return $avatar;
    }

    private function isGifCompatible(string $avatar): bool
    {
        return str_starts_with($avatar, 'a_');
    }
}
