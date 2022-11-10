<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Faq;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaqPolicy
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
        return in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_SUPPORTER]);
    }

    /**
     * Determine whether the user can view the faq.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Faq  $faq
     * @return mixed
     */
    public function view(User $user, Faq $faq)
    {
        return true;
    }

    /**
     * Determine whether the user can create faqs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_SUPPORTER])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the faq.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Faq  $faq
     * @return mixed
     */
    public function update(User $user, Faq $faq)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_SUPPORTER])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the faq.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Faq  $faq
     * @return mixed
     */
    public function delete(User $user, Faq $faq)
    {
        if (in_array($user->role_id, [User::ROLE_ADMIN, User::ROLE_EDITOR])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the faq.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Faq  $faq
     * @return mixed
     */
    public function restore(User $user, Faq $faq)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the faq.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Faq  $faq
     * @return mixed
     */
    public function forceDelete(User $user, Faq $faq)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
