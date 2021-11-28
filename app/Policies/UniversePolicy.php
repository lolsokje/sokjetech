<?php

namespace App\Policies;

use App\Models\Universe;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UniversePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Universe $universe
     *
     * @return bool
     */
    public function view(User $user, Universe $universe): bool
    {
        return true;
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
        return $user->id === $universe->user_id;
    }
}
