<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\User;

class AfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && auth()->user()->role_id === User::ROLE_TEAM) {
            // TODO: Find teams user is member in
            $memberships = Member::where('user_id', '=', auth()->id())->get();
            // Directly log user into account if there is only one team (default)
            if ($memberships->count() == 1) {
                $supported = User::findOrFail($memberships[0]->team->owner_id);
                Auth::user()->impersonate($supported);
            }
        }

        return $response;
    }
}
