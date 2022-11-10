<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;
use App\Models\PlayerConfig;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlayerConfigPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any player configs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can view the player config.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlayerConfig  $playerConfig
     * @return mixed
     */
    public function view(User $user, PlayerConfig $playerConfig)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }

        return $user->usr_id === $playerConfig->user_id;
    }

    /**
     * Determine whether the user can create player configs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }

        $count = get_package_feature_player_configuration($user->package, $user);

        return has_package_feature($user->package, Package::FEATURE_PLAYER_CONFIGURATION)
            && $count['available'] > 0;
    }

    /**
     * Determine whether the user can update the player config.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlayerConfig  $playerConfig
     * @return mixed
     */
    public function update(User $user, PlayerConfig $playerConfig)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }

        return $user->usr_id === $playerConfig->user_id;
    }

    /**
     * Determine whether the user can delete the player config.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlayerConfig  $playerConfig
     * @return mixed
     */
    public function delete(User $user, PlayerConfig $playerConfig)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }

        return $user->usr_id === $playerConfig->user_id;
    }

    /**
     * Determine whether the user can restore the player config.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlayerConfig  $playerConfig
     * @return mixed
     */
    public function restore(User $user, PlayerConfig $playerConfig)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the player config.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlayerConfig  $playerConfig
     * @return mixed
     */
    public function forceDelete(User $user, PlayerConfig $playerConfig)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
