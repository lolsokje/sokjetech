<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\RedirectResponse as Redirect;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback(): Redirect
    {
        $discordUser = Socialite::driver('discord')->user();

        $user = User::updateOrCreate([
            'discord_id' => $discordUser->getId(),
        ], [
            'username' => $discordUser->getName(),
            'avatar' => $this->getAvatar($discordUser),
            'is_admin' => in_array($discordUser->getId(), config('services.discord.admin_ids')),
        ]);

        Auth::login($user);

        return redirect(route('index'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect(route('index'));
    }

    private function getAvatar(SocialiteUser $user): string
    {
        // getAvatar() will always return a string, even if no avatar exists. The user array contains null if no avatar
        // is set by the user, so that value is checked before assigning the variable.
        $userAvatar = $user->user['avatar'];
        $avatar = $userAvatar ? $user->getAvatar() : null;

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
