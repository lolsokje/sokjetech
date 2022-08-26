<?php

namespace App\Policies;

use App\Enums\UniverseVisibility;
use App\Models\Universe;
use App\Models\User;
use Gate;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniversePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User|null $user
     * @param Universe $universe
     *
     * @return bool
     */
    public function view(?User $user, Universe $universe): bool
    {
        $visibility = $universe->visibility;

        return match ($visibility) {
            UniverseVisibility::PUBLIC => true,
            UniverseVisibility::AUTH => $user !== null,
            UniverseVisibility::PRIVATE => $user?->id === $universe->user_id,
        };
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Universe $universe
     *
     * @return bool
     */
    public function update(User $user, Universe $universe): bool
    {
        return Gate::check('owns-universe', $universe);
    }
}
