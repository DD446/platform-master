<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        if (Gate::forUser(auth()->user())->denies('viewSettings')) {
            abort(403);
        }

        \SEO::setTitle(trans('settings.page_title'));

        return view('settings.index');
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
     * @param  \App\Models\UserPreference  $userPreference
     * @return \Illuminate\Http\Response
     */
    public function show(UserPreference $userPreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserPreference  $userPreference
     * @return \Illuminate\Http\Response
     */
    public function edit(UserPreference $userPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserPreference  $userPreference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserPreference $userPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserPreference  $userPreference
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserPreference $userPreference)
    {
        //
    }
}
