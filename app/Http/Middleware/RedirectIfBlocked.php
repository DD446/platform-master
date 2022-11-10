<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class RedirectIfBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && auth()->user()->is_blocked) {
            $manager = app('impersonate');
            if ($manager->isImpersonating()) {
                $impersonator = User::find($manager->getImpersonatorId());

                // Allow users with special rights to access any page
                if (in_array($impersonator->role_id, [User::ROLE_EDITOR, User::ROLE_SUPPORTER, User::ROLE_ADMIN])) {
                    return $response;
                }
            }

            if (!in_array(auth()->user()->role_id, [User::ROLE_EDITOR, User::ROLE_SUPPORTER, User::ROLE_ADMIN])
                && !in_array(\Route::currentRouteName(), ['accounting.create', 'accounting.index', 'rechnung.index', 'rechnung.show', 'billing', 'bills.download', 'contactus.create', 'contactus.store', 'package.delete', 'package.destroy', 'faq.index', 'faq.show', 'faq.category', 'faq.search'])
                && !in_array(\Route::currentRouteAction(), ['PUT'])
                && \Route::currentRouteName()) {

                return redirect()->route('accounting.create')->with(['error' => trans('bills.account_blocked')]);
            }
        }

        return $response;
    }
}
