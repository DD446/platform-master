<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberQueue;
use App\Models\Package;
use App\Models\Team;
use App\Models\UserExtra;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        \SEO::setTitle(trans('teams.page_title'));

        $members = [
            'included' => 0,
            'extra' => 0,
            'total' => 0,
            'used' => 0,
            'available' => 0,
            'queued' => 0,
            'usedWithQueued' => 0,
        ];
        $canAddMembers = has_package_feature(auth()->user()->package, Package::FEATURE_MEMBERS);

        if ($canAddMembers) {
            //$members = self::getMemberCount(auth()->user()->usr_id);
            $members = get_package_feature_members(auth()->user()->package, /** User */ auth()->user());
        }

        return view('teams.index', compact('members', 'canAddMembers'));
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
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        //
    }

    /**
     * @return mixed
     * @deprecated
     */
    protected function getNumberMembersAllowed()
    {
        $members = get_package_feature_members(auth()->user()->package, /** User */ auth()->user());

        return $members['total'];
    }
}
