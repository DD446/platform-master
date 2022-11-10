<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CampaignInvitation;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignInvitationPolicy
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
     * Determine whether the user can view the campaign invitation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return mixed
     */
    public function view(User $user, CampaignInvitation $campaignInvitation)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->usr_id === $campaignInvitation->campaign->advertiser_id;
    }

    /**
     * Determine whether the user can create campaign invitations.
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
     * Determine whether the user can update the campaign invitation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return mixed
     */
    public function update(User $user, CampaignInvitation $campaignInvitation)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->usr_id === $campaignInvitation->campaign->advertiser_id;
    }

    /**
     * Determine whether the user can delete the campaign invitation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return mixed
     */
    public function delete(User $user, CampaignInvitation $campaignInvitation)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->usr_id === $campaignInvitation->campaign->advertiser_id;
    }

    /**
     * Determine whether the user can restore the campaign invitation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return mixed
     */
    public function restore(User $user, CampaignInvitation $campaignInvitation)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the campaign invitation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CampaignInvitation  $campaignInvitation
     * @return mixed
     */
    public function forceDelete(User $user, CampaignInvitation $campaignInvitation)
    {
        return $user->role_id === User::ROLE_ADMIN;
    }
}
