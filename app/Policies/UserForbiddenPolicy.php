<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserForbidden;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserForbiddenPolicy
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
        return in_array($user->role_id, [User::ROLE_ADMIN]);

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserForbidden  $userForbidden
     * @return mixed
     */
    public function view(User $user, UserForbidden $userForbidden)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserForbidden  $userForbidden
     * @return mixed
     */
    public function update(User $user, UserForbidden $userForbidden)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserForbidden  $userForbidden
     * @return mixed
     */
    public function delete(User $user, UserForbidden $userForbidden)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserForbidden  $userForbidden
     * @return mixed
     */
    public function restore(User $user, UserForbidden $userForbidden)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserForbidden  $userForbidden
     * @return mixed
     */
    public function forceDelete(User $user, UserForbidden $userForbidden)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }
}
