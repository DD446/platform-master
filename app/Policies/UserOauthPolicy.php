<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserOauth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserOauthPolicy
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
     * @param  \App\Models\UserOauth  $userOauth
     * @return mixed
     */
    public function view(User $user, UserOauth $userOauth)
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
        return in_array($user->role_id, [User::ROLE_USER, User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserOauth  $userOauth
     * @return mixed
     */
    public function update(User $user, UserOauth $userOauth)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserOauth  $userOauth
     * @return mixed
     */
    public function delete(User $user, UserOauth $userOauth)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN])
            || $userOauth->user_id == $user->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserOauth  $userOauth
     * @return mixed
     */
    public function restore(User $user, UserOauth $userOauth)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserOauth  $userOauth
     * @return mixed
     */
    public function forceDelete(User $user, UserOauth $userOauth)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN])
            || $userOauth->user_id == $user->user_id;
    }
}
