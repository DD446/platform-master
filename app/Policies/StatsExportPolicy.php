<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\StatsExport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatsExportPolicy
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
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatsExport  $statsExport
     * @return mixed
     */
    public function view(User $user, StatsExport $statsExport)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])) {
            return true;
        }

        return $user->id === $statsExport->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])) {
            return true;
        }

        return has_package_feature($user->package, Package::FEATURE_STATISTICS_EXPORT) &&
            $user->available_stats_exports > 0;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatsExport  $statsExport
     * @return mixed
     */
    public function update(User $user, StatsExport $statsExport)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatsExport  $statsExport
     * @return mixed
     */
    public function delete(User $user, StatsExport $statsExport)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])) {
            return true;
        }

        return $user->id === $statsExport->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatsExport  $statsExport
     * @return mixed
     */
    public function restore(User $user, StatsExport $statsExport)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StatsExport  $statsExport
     * @return mixed
     */
    public function forceDelete(User $user, StatsExport $statsExport)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }
}
