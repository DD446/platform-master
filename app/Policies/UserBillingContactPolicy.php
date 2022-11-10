<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserBillingContact;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserBillingContactPolicy
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
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return mixed
     */
    public function view(User $user, UserBillingContact $userBillingContact)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])
            || $user->user_id === $userBillingContact->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER, User::ROLE_USER]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return mixed
     */
    public function update(User $user, UserBillingContact $userBillingContact)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN])
            || $user->user_id === $userBillingContact->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return mixed
     */
    public function delete(User $user, UserBillingContact $userBillingContact)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN])
            || $user->user_id === $userBillingContact->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return mixed
     */
    public function restore(User $user, UserBillingContact $userBillingContact)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserBillingContact  $userBillingContact
     * @return mixed
     */
    public function forceDelete(User $user, UserBillingContact $userBillingContact)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }
}
