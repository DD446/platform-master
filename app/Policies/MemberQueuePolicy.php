<?php

namespace App\Policies;

use App\Models\MemberQueue;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberQueuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return mixed
     */
    public function view(User $user, MemberQueue $memberQueue)
    {
        return $user->role_id === User::ROLE_ADMIN
            || $user->user_id === $memberQueue->owner()->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role_id == User::ROLE_GUEST // guest
            || $user->role_id == User::ROLE_ADMIN; // admin
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return mixed
     */
    public function update(User $user, MemberQueue $memberQueue)
    {
        return $user->role_id === User::ROLE_ADMIN
            || $user->user_id === $memberQueue->owner()->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return mixed
     */
    public function delete(User $user, MemberQueue $memberQueue)
    {
        return $user->role_id === User::ROLE_ADMIN
            || $user->user_id === $memberQueue->owner()->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return mixed
     */
    public function restore(User $user, MemberQueue $memberQueue)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return mixed
     */
    public function forceDelete(User $user, MemberQueue $memberQueue)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
