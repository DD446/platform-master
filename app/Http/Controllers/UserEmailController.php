<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Requests\UserEmailRequest;
use App\Models\UserEmailQueue;

class UserEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $ueq = UserEmailQueue::owner()->orderByDesc('id')->first();

        return view('user.email', compact('ueq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserEmailRequest  $userEmailRequest
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function create(UserEmailRequest $userEmailRequest)
    {
        UserEmailQueue::owner()->delete();

        $validated = $userEmailRequest->validated();
        $ueq = new UserEmailQueue();
        $ueq->user_id = auth()->user()->user_id;
        $ueq->email = $validated['newemail'];
        $ueq->hash = Str::random(6);
        $ueq->date_created = Carbon::now();
        $ueq->saveOrFail();

        $msg = ['success' => trans('user.message_success_user_email_update')];

        return redirect()->back()->with($msg);
    }

    public function update($hash)
    {
        // Check if there is a dataset for the logged in user
        // If not fail
        $ueq = UserEmailQueue::owner()->whereHash($hash)->firstOrFail();

        // Update the users email
        $user = auth()->user();
        $user->email = $ueq->email;
        $user->saveOrFail();

        UserEmailQueue::owner()->delete();

        $msg = ['success' => trans('user.message_success_user_email_updated', ['email' => $user->email])];

        return redirect()->route('settings.index')->with($msg);
    }
}
