<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Member;
use App\Models\MemberQueue;
use App\Models\Team;
use App\Models\User;

class MemberQueueController extends Controller
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
        // TODO: Assumes we have only one team for now
        if ($teams) {
            $members = $teams[0]->queuedMembers;
            if ($members) {
                $data = $members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'email' => $member->email,
                    ];
                });
            }
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $userId = auth()->id();
        // For the moment there will be only one team per user
        // so this works fine
        // TODO: later pass the correct team via form request
        $team = Team::firstOrCreate(['owner_id' => $userId]);

        $msg = ['success' => trans('teams.text_message_success_added_invite')];

        $mq = MemberQueue::firstOrNew(['email' => \request('email'), 'team_id' => $team->id]);
        $mq->hash = Str::random(6);

        if (!$mq->saveOrFail()) {
            $msg = ['error' => trans('teams.text_message_error_adding_invite')];
        }

        return response()->json($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(string $invite, string $id)
    {
        \Illuminate\Support\Facades\Log::debug("INVITE $invite - ID $id");
        $memberQueue = MemberQueue::where('id', '=', $id)->where('hash', '=', $invite)->firstOrFail();

        if (auth()->check()) {
            // If the user is already logged in with an account
            // that does not have the same email as the invited one
            // there is a strange behavior
            // so log out the user to make sure the invite is created correctly
            \auth()->logout();
        }

        // Check if there is an account for this email already
        $user = User::firstOrNew(['email' => $memberQueue->email]);
        $userExisted = $user->exists;

        if (!$userExisted) {
            $user->username  = User::getNewUsername();
            $user->role_id = $memberQueue->role_id;
            $user->is_acct_active = 1;
            $user->package_id = 0;
            $user->saveOrFail();
        }

        $member = new Member();
        $member->user_id = $user->id;
        $member->team_id = $memberQueue->team_id;
        $member->saveOrFail();

        $team = $memberQueue->team;
        $memberQueue->delete();

        if ($userExisted) {
            return response()->redirectToRoute('home')->with([
                'status' => trans('teams.text_message_success_added_member',
                    ['name' => $team->user->fullname, 'email' => $team->user->email])
            ]);
        }

        Auth::loginUsingId($user->id);

        return view('teams.invite', ['hideNav' => true]);
    }

    public function edit(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required|string|min:2',
            'lastname' => 'nullable|string|min:2',
            'password' => 'required|string|min:6',
            'terms' => 'required|in:on',
        ]);

        auth()->user()->update([
            'first_name' => request('firstname'),
            'last_name' => request('lastname'),
            'passwd' => md5($request->get('password')),
            'password' => password_hash($request->get('password'), PASSWORD_BCRYPT),
            'terms_date' => Carbon::now(),
            'privacy_date' => Carbon::now(),
            'terms_version' => User::TERMS_VERSION,
            'privacy_version' => User::PRIVACYPOLICY_VERSION,
        ]);

        return response()->redirectToRoute('home')->with(['status' => trans('teams.text_message_success_updated_data')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $memberQueue = MemberQueue::findOrFail($id);

        if ($memberQueue->team->owner_id !== auth()->id()) {
            abort(403);
        }
        $memberQueue->hash = Str::random(6);
        $msg = ['success' => trans('teams.text_message_success_resend_invite')];

        if (!$memberQueue->saveOrFail()) {
            $msg = ['error' => trans('teams.text_message_error_adding_invite')];
        }

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberQueue  $memberQueue
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $memberQueue = MemberQueue::findOrFail($id);

        if ($memberQueue->team->owner_id !== auth()->user()->usr_id) {
            abort(403);
        }
        $msg = ['success' => trans('teams.text_message_success_delete_invite')];

        if (!$memberQueue->delete()) {
            $msg = ['error' => trans('teams.text_message_error_deleteing_invite')];
        }

        return response()->json($msg);
    }
}
