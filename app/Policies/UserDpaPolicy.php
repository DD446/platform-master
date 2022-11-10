<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserDpa;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserDpaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return in_array($user->role_id, [
            User::ROLE_ADMIN,
            User::ROLE_SUPPORTER
        ]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, UserDpa $model)
    {
        return $user->usr_id == $model->usr_id
            || in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER]);
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
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, UserDpa $model)
    {
        return $user->usr_id == $model->usr_id
            || $user->role_id == User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, UserDpa $model)
    {
        return $user->usr_id == $model->usr_id
            || $user->role_id == User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, UserDpa $model)
    {
        return $user->role_id == User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, UserDpa $model)
    {
        return $user->role_id == User::ROLE_ADMIN;
    }
}
