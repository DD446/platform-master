<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user
     *
     * Fetches details about authenticated user.
     *
     * @group User
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response|UserResource
     */
    public function index()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Update podcast
     *
     * Updates details of a podcast. Accessible with scope: feeds
     *
     * @group Podcasts
     * @urlParam feed_id string required ID of the podcast (feed). Example: beispiel
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UserRequest $request)
    {
        $user = auth()->user();
        $msg = ['success' => trans('profile.success_message_contactdata_updated')];

        if (!$user->update($request->validated())) {
            $msg = ['error' => trans('profile.error_message_contactdata_updated')];
        }

        return response()->json($msg);
    }

    public function destroy(UserDeleteRequest $request)
    {
        // TODO: Delete event to clean up data
        $user = auth()->user();
        //$user->delete();
        // TODO: Logout
    }
}
