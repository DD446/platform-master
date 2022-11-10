<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserUpload;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserExtraPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the news.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR]);
    }

    /**
     * Determine whether the user can view the news.
     *
     * @param  \App\Models\User  $user
     * @param  UserExtra  $extra
     * @return mixed
     */
    public function view(User $user, UserExtra $extra)
    {
        return true;
    }

    /**
     * Determine whether the user can create news.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_USER])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the news.
     *
     * @param  \App\Models\User  $user
     * @param  UserExtra  $extra
     * @return mixed
     */
    public function update(User $user, UserExtra $extra)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])
            || $user->id === $extra->user_id
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the news.
     *
     * @param  \App\Models\User  $user
     * @param  UserExtra  $extra
     * @return mixed
     */
    public function delete(User $user, UserExtra $extra)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->id === $extra->user_id
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the news.
     *
     * @param  \App\Models\User  $user
     * @param  UserExtra  $extra
     * @return mixed
     */
    public function restore(User $user, UserExtra $extra)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the news.
     *
     * @param  \App\Models\User  $user
     * @param  UserExtra  $extra
     * @return mixed
     */
    public function forceDelete(User $user, UserExtra $extra)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
