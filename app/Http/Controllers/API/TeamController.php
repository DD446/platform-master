<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamCollection;
use App\Models\Feed;
use App\Models\Package;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    /**
     * List teams
     *
     * Returns a list of teams. Accessible with scopes: teams,read-only-teams *Attention* The `Example request` is not correct for this parameter. It should look like this: page[number]=5&page[size]=30 Example: page[number]=5&page[size]=30
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

        $teams = Team::owner()->jsonPaginate();

        return new TeamCollection($teams);
    }

    /**
     * Get a team
     *
     * Get details for a team
     * @group Teams
     *
     * @urlParam team_id integer required ID des Teams. Example: 4
     */
    public function show()
    {

    }

    /**
     * Add team
     *
     * Create a new team.
     *
     * @group Teams
     */
    public function store(TeamRequest $request)
    {

    }

    /**
     * Update team
     *
     * Give a team a new name.
     *
     * @group Teams
     * @urlParam team_id integer required ID des Teams. Example: 4
     */
    public function update(TeamRequest $request)
    {

    }

    /**
     * Delete team
     *
     * Deletes a team with its members (but doesn't delete the members' accounts).
     *
     * @group Teams
     * @urlParam team_id integer required ID des Teams Example: praktikanten
     */
    public function destroy($team_id)
    {

    }


    /**
     * Copy team
     *
     * Creates a copy of a team
     *
     * @group Teams
     * @urlParam team_id string required ID des Teams Example: praktikanten
     * @queryParam name string required Name of the new team. Example: volontaere
     */
    public function copy($team_id)
    {
        $team = Team::owner()->findOrFail($team_id);

    }
}
