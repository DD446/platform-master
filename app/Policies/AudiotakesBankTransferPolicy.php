<?php

namespace App\Policies;

use App\Models\AudiotakesBankTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AudiotakesBankTransferPolicy
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
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesBankTransfer  $audiotakesBankTransfer
     * @return mixed
     */
    public function view(User $user, AudiotakesBankTransfer $audiotakesBankTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesBankTransfer->user_id;
    }

    /**
     * Determine whether the user can create models.
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesBankTransfer  $audiotakesBankTransfer
     * @return mixed
     */
    public function update(User $user, AudiotakesBankTransfer $audiotakesBankTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesBankTransfer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesBankTransfer  $audiotakesBankTransfer
     * @return mixed
     */
    public function delete(User $user, AudiotakesBankTransfer $audiotakesBankTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesBankTransfer->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesBankTransfer  $audiotakesBankTransfer
     * @return mixed
     */
    public function restore(User $user, AudiotakesBankTransfer $audiotakesBankTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesBankTransfer  $audiotakesBankTransfer
     * @return mixed
     */
    public function forceDelete(User $user, AudiotakesBankTransfer $audiotakesBankTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
