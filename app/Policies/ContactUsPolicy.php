<?php

namespace App\Policies;

use App\Models\ContactUs;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactUsPolicy
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
     * @param  \App\Models\ContactUs  $contactUs
     * @return mixed
     */
    public function view(User $user, ContactUs $contactUs)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(?User $user)
    {
        if (!$user) {
            return true;
        }

        return in_array(optional($user)->role_id, [User::ROLE_ADMIN, User::ROLE_USER, User::ROLE_GUEST, User::ROLE_TEAM]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactUs  $contactUs
     * @return mixed
     */
    public function update(User $user, ContactUs $contactUs)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactUs  $contactUs
     * @return mixed
     */
    public function delete(User $user, ContactUs $contactUs)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactUs  $contactUs
     * @return mixed
     */
    public function restore(User $user, ContactUs $contactUs)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactUs  $contactUs
     * @return mixed
     */
    public function forceDelete(User $user, ContactUs $contactUs)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }
}
