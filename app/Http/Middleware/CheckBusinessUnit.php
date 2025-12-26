<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBusinessUnit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$allowedBU  List of allowed business unit codes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$allowedBU)
    {
        $user = Auth::user();

        // 🔥 Super Admin & Admin bypass
        if (isSuperAdmin() || isAdmin()) {
            return $next($request);
        }

        // ❌ User has no business unit assigned
        if (!$user->businessUnit) {
            abort(403, 'Business Unit not assigned');
        }

        // ✅ If no specific BU restriction, allow
        if (empty($allowedBU)) {
            return $next($request);
        }

        // ❌ Deny if user's BU code not in allowed list
        if (!in_array($user->businessUnit->code, $allowedBU)) {
            abort(403, 'Access restricted to specific Business Unit(s)');
        }

        return $next($request);
    }
}
