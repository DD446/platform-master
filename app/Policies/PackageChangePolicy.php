<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\PackageChange;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageChangePolicy
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
     * @param  \App\Models\Package  $change
     * @return mixed
     */
    public function view(User $user, PackageChange $change)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return auth()->check();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Package  $change
     * @return mixed
     */
    public function update(User $user, PackageChange $change)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Package  $change
     * @return mixed
     */
    public function delete(User $user, PackageChange $change)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Package  $change
     * @return mixed
     */
    public function restore(User $user, PackageChange $change)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Package  $change
     * @return mixed
     */
    public function forceDelete(User $user, PackageChange $change)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
