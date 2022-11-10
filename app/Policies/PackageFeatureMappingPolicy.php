<?php

namespace App\Policies;

use App\Models\PackageFeatureMapping;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageFeatureMappingPolicy
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
     * @param  \App\Models\PackageFeatureMapping  $packageFeature
     * @return mixed
     */
    public function view(User $user, PackageFeatureMapping $packageFeature)
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
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PackageFeatureMapping  $packageFeature
     * @return mixed
     */
    public function update(User $user, PackageFeatureMapping $packageFeature)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PackageFeatureMapping  $packageFeature
     * @return mixed
     */
    public function delete(User $user, PackageFeatureMapping $packageFeature)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PackageFeatureMapping  $packageFeature
     * @return mixed
     */
    public function restore(User $user, PackageFeatureMapping $packageFeature)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PackageFeatureMapping  $packageFeature
     * @return mixed
     */
    public function forceDelete(User $user, PackageFeatureMapping $packageFeature)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
