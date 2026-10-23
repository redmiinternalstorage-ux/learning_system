<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FullAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all abilities to admin and regular user.
     */
    public function before(User $user, $ability)
    {
        return in_array($user->role, ['admin', 'user']) ? true : null;
    }
}
?>