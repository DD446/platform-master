<?php

namespace App\Policies;

use App\Models\AudiotakesContract;
use App\Models\AudiotakesPodcasterTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AudiotakesPodcasterTransferPolicy
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
     * @param  \App\Models\AudiotakesPodcasterTransfer  $audiotakesPodcasterTransfer
     * @return mixed
     */
    public function view(User $user, AudiotakesPodcasterTransfer $audiotakesPodcasterTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesPodcasterTransfer->user_id;
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

        if (AudiotakesContract::owner()->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesPodcasterTransfer  $audiotakesPodcasterTransfer
     * @return mixed
     */
    public function update(User $user, AudiotakesPodcasterTransfer $audiotakesPodcasterTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesPodcasterTransfer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesPodcasterTransfer  $audiotakesPodcasterTransfer
     * @return mixed
     */
    public function delete(User $user, AudiotakesPodcasterTransfer $audiotakesPodcasterTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN
            || $user->email == 'bastian@audiotakes.net') {
            return true;
        }
        return $user->id === $audiotakesPodcasterTransfer->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AudiotakesPodcasterTransfer  $audiotakesPodcasterTransfer
     * @return mixed
     */
    public function restore(User $user, AudiotakesPodcasterTransfer $audiotakesPodcasterTransfer)
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
     * @param  \App\Models\AudiotakesPodcasterTransfer  $audiotakesPodcasterTransfer
     * @return mixed
     */
    public function forceDelete(User $user, AudiotakesPodcasterTransfer $audiotakesPodcasterTransfer)
    {
        if ($user->role_id === User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
