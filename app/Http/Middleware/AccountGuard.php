<?php

namespace App\Http\Middleware;

use App\Support\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 1) Signs out anyone who is no longer Active (Exited / Inactive), even if they were already signed in.
 * 2) Sends people still on the starting password (Welcome@123) to "Change password" until they change it.
 *    The check runs once per session and is remembered in the session.
 */
class AccountGuard
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if (! AccountAccess::canSignIn($user)) {
            $message = AccountAccess::blockedMessage($user);
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                abort(401, $message);
            }

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        if (! $request->session()->has('pw_default')) {
            $request->session()->put('pw_default', AccountAccess::usesDefaultPassword($user));
        }

        if ($request->session()->get('pw_default') && ! $request->routeIs('me.password', 'me.password.update', 'update_password', 'logout')) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, 'Please change your password first.');
            }

            return redirect()->route('me.password');
        }

        return $next($request);
    }
}
