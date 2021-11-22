<?php

namespace App\Policies;

use App\Models\Circuit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CircuitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can alter (update or delete) the model.
     *
     * @param User $user
     * @param Circuit $circuit
     *
     * @return bool
     */
    public function alter(User $user, Circuit $circuit): bool
    {
        return $user->id === $circuit->user_id;
    }
}
