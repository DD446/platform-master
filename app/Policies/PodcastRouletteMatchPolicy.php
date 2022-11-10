<?php

namespace App\Policies;

use App\Models\PodcastRouletteMatch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PodcastRouletteMatchPolicy
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
     * @param  \App\Models\PodcastRouletteMatch  $podcastRouletteMatch
     * @return mixed
     */
    public function view(User $user, PodcastRouletteMatch $podcastRouletteMatch)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }

        return $podcastRouletteMatch->is_public;
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
     * @param  \App\Models\PodcastRouletteMatch  $podcastRouletteMatch
     * @return mixed
     */
    public function update(User $user, PodcastRouletteMatch $podcastRouletteMatch)
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
     * @param  \App\Models\PodcastRouletteMatch  $podcastRouletteMatch
     * @return mixed
     */
    public function delete(User $user, PodcastRouletteMatch $podcastRouletteMatch)
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
     * @param  \App\Models\PodcastRouletteMatch  $podcastRouletteMatch
     * @return mixed
     */
    public function restore(User $user, PodcastRouletteMatch $podcastRouletteMatch)
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
     * @param  \App\Models\PodcastRouletteMatch  $podcastRouletteMatch
     * @return mixed
     */
    public function forceDelete(User $user, PodcastRouletteMatch $podcastRouletteMatch)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN])) {
            return true;
        }

        return false;
    }
}
