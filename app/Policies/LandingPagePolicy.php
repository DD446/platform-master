<?php

namespace App\Policies;

use App\Models\LandingPage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LandingPagePolicy
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
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LandingPage  $landingPage
     * @return mixed
     */
    public function view(User $user, LandingPage $landingPage)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return $landingPage->is_public;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LandingPage  $landingPage
     * @return mixed
     */
    public function update(User $user, LandingPage $landingPage)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LandingPage  $landingPage
     * @return mixed
     */
    public function delete(User $user, LandingPage $landingPage)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LandingPage  $landingPage
     * @return mixed
     */
    public function restore(User $user, LandingPage $landingPage)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LandingPage  $landingPage
     * @return mixed
     */
    public function forceDelete(User $user, LandingPage $landingPage)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }
}
