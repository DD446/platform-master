<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * List members
     *
     * Returns a list of members of a team. Accessible with scopes: teams,read-only-teams,members,read-only-members *Attention* The `Example request` is not correct for this parameter. It should look like this: page[number]=5&page[size]=30 Example: page[number]=5&page[size]=30
     *
     * @group Teams
     * @apiResourceCollection App\Http\Resources\TeamCollection
     * @apiResourceModel App\Models\Team
     * @queryParam page string[] Used for pagination. You can pass "number" and "size". Default: page[number] =1, page[size] = 30,
     *
     * @return TeamCollection
     * @throws \Exception
     */
    public function index()
    {
        $canAddMembers = has_package_feature(auth()->user()->package, Package::FEATURE_MEMBERS);

        if (!$canAddMembers) {
            abort(404, trans('teams.text_info_missing_feature', ['route' => route('packages')]));
        }
    }

    /**
     * Get member
     *
     * List details of a member
     *
     * @group Teams
     * @urlParam member_id integer required ID of the member. Example: 43
     */
    public function show()
    {

    }

    /**
     * Add member
     *
     * Create a new team member. Sends out an invitation to the given email address which needs to be confirmed if there isn't already an account for this email.
     *
     * @group Teams
     */
    public function store(MemberRequest $request)
    {

    }

    /**
     * Delete member
     *
     * Removes a member from a team (but doesn't delete the user's account).
     *
     * @group Teams
     * @urlParam member_id integer required ID of the member. Example: 43
     */
    public function destroy()
    {

    }
}
