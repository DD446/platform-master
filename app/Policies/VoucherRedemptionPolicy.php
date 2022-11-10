<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VoucherRedemption;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoucherRedemptionPolicy
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
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VoucherRedemption  $voucher
     * @return mixed
     */
    public function view(User $user, VoucherRedemption $voucher)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN])
            || auth()->check();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VoucherRedemption  $voucher
     * @return mixed
     */
    public function update(User $user, VoucherRedemption $voucher)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VoucherRedemption  $voucher
     * @return mixed
     */
    public function delete(User $user, VoucherRedemption $voucher)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VoucherRedemption  $voucher
     * @return mixed
     */
    public function restore(User $user, VoucherRedemption $voucher)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VoucherRedemption  $voucher
     * @return mixed
     */
    public function forceDelete(User $user, VoucherRedemption $voucher)
    {
        return in_array($user->role_id, [User::ROLE_ADMIN]);
    }
}
