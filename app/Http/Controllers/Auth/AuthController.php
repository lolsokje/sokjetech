<?php

namespace App\Http\Controllers\Auth;

use App\Actions\GetUserAvatar;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse as Redirect;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class AuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        session(['redirect' => url()->previous()]);

        return Socialite::driver('discord')->redirect();
    }

    /**
     * @throws Throwable
     */
    public function callback(): Redirect
    {
        $discordUser = Socialite::driver('discord')->user();

        $this->verifyAccessToStaging($discordUser);

        $user = User::updateOrCreate([
            'discord_id' => $discordUser->getId(),
        ], [
            'username' => $discordUser->getName(),
            'avatar' => (new GetUserAvatar($discordUser))->handle(),
            'is_admin' => $this->isAdmin($discordUser),
        ]);

        Auth::login($user, true);

        return $this->handleRedirect();
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect(route('index'));
    }

    private function isAdmin(SocialiteUser $user): bool
    {
        return in_array($user->getId(), config('services.discord.admin_ids'));
    }

    /**
     * @throws Throwable
     */
    private function verifyAccessToStaging(SocialiteUser $user): void
    {
        if (config('app.env') !== 'staging' || $this->isAdmin($user)) {
            return;
        }

        $stagingUser = in_array($user->getId(), config('services.discord.staging_ids'));
        throw_if(! $stagingUser, new Exception('No access to the staging environment'));
    }

    private function handleRedirect(): Redirect
    {
        $redirect = session()->get('redirect');

        if ($redirect) {
            session()->forget('redirect');

            return redirect($redirect);
        }

        return redirect()->route('index');
    }
}
