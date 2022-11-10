<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Feed;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedPolicy
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
     * Determine whether the user can view the feed.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feed  $feed
     * @return mixed
     */
    public function view(User $user, Feed $feed)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER, User::ROLE_EDITOR])
            || $user->username == $feed->username;
    }

    /**
     * Determine whether the user can create feeds.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return $user->role == User::ROLE_USER
            && $user->is_acct_active == 1;
    }

    /**
     * Determine whether the user can update the feed.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feed  $feed
     * @return mixed
     */
    public function update(User $user, Feed $feed)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return $user->is_acct_active == 1
            && $user->username == $feed->username;
    }

    /**
     * Determine whether the user can delete the feed.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feed  $feed
     * @return mixed
     */
    public function delete(User $user, Feed $feed)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return $user->is_acct_active == 1
            && $user->username == $feed->username;
    }
}
