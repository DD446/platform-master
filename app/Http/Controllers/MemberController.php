<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Member;
use App\Models\MemberQueue;
use App\Models\Team;
use App\Models\User;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::owner()->get();
        $data = [];
        // TODO: We work only with the default team right now
        if ($teams) {
            $members = $teams[0]->members;
            if ($members) {
                $data = $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'email' => $member->user->email,
                        'name' => $member->user->fullname,
                    ];
                });
            }
        }

        return response()->json($data);
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
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        if ($member->team->owner_id !== auth()->id()) {
            abort(403);
        }
        $msg = ['success' => trans('teams.text_message_success_delete_member')];

        if (!$member->forceDelete()) {
            $msg = ['error' => trans('teams.text_message_error_deleteing_member')];
        }

        return response()->json($msg);
    }

    public function projects()
    {
        $aMember = Member::where('user_id', '=', auth()->id())->get();

        $data = $aMember->map(function ($owner) {
            return [
                'id' => $owner->id,
                'email' => $owner->team->user->email,
                'name' => $owner->team->user->fullname,
            ];
        });

        return response()->json($data);
    }

    public function login($id)
    {
        $member = Member::where('user_id', '=', auth()->user()->usr_id)->where('id', '=', $id)->firstOrFail();
        $supported = User::findOrFail($member->team->owner_id);
        Auth::user()->impersonate($supported);

        return redirect()->route('home');
    }

    public function count()
    {
        $members = get_package_feature_members(auth()->user()->package, /** User */ auth()->user());
        //$aCount = TeamController::getMemberCount(auth()->user()->usr_id);

        return response()->json($members);
    }
}
