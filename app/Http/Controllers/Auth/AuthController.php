<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\RedirectResponse as Redirect;
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
            'discord_id' => $discordUser->getId()
        ], [
            'username' => $discordUser->getName(),
            'avatar' => $discordUser->getAvatar(),
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
}
