<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** KeyLeads is for active sales staff and admins (FOS, TL, Sr. TL, CH, Admin, Superadmin). */
class EnsureCrmUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive. Please contact admin.']);
        }

        if ($user && ! $user->usesCrm()) {
            return redirect()->route('dashboard')->with('error', 'Leads are only for the sales team.');
        }

        return $next($request);
    }
}
