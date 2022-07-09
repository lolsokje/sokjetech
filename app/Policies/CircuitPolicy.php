<?php

namespace App\Policies;

use App\Models\Circuit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CircuitPolicy
{
    use HandlesAuthorization;

    public function alter(User $user, Circuit $circuit): bool
    {
        return (int)$user->id === (int)$circuit->user_id;
    }
}
