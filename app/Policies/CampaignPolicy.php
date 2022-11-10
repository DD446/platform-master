<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function view(User $user, Campaign $campaign)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return false;

        if (in_array($user->role_id, [User::ROLE_SUPPORTER, User::ROLE_EDITOR])) {
            return false;
        }

        return $user->usr_id === $campaign->advertiser_id;
    }

    /**
     * Determine whether the user can create campaigns.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->is_advertiser === true;
    }

    /**
     * Determine whether the user can update the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function update(User $user, Campaign $campaign)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->usr_id === $campaign->advertiser_id;
    }

    /**
     * Determine whether the user can delete the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function delete(User $user, Campaign $campaign)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->usr_id === $campaign->advertiser_id;
    }

    /**
     * Determine whether the user can restore the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function restore(User $user, Campaign $campaign)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function forceDelete(User $user, Campaign $campaign)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
