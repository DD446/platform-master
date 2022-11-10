<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
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
     * Determine whether the user can view the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return mixed
     */
    public function view(User $user, Review $review)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create reviews.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return mixed
     */
    public function update(User $user, Review $review)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return mixed
     */
    public function delete(User $user, Review $review)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return mixed
     */
    public function restore(User $user, Review $review)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return mixed
     */
    public function forceDelete(User $user, Review $review)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
