<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDeleteRequest;
use App\Rules\PasswordIsCorrect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        \SEO::setTitle(trans('user.page_title'));

        return view('user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Get user
     *
     * Fetches details about authenticated user.
     *
     * @group User
     * @apiResource UserResource
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response|UserResource
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDeleteRequest $request)
    {
        $entity = auth()->id();
        $email = auth()->user()->email;
        $type = 11;

        Auth::logout();

        if (!User::find($entity)->delete()) {
            Session::flash('error', trans('user.message_failure_package_deleted'));
        } else {
            // TODO: Send confirmation
            Session::flash('success', trans('user.message_success_package_deleted'));
        }

        return response()->view('package.destroy', compact('entity', 'type'));
    }
}
