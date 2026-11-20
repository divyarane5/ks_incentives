<?php

namespace App\Http\Middleware;

use App\Support\Modules;
use Closure;
use Illuminate\Http\Request;

/** Keeps each RealtySuite module to the people who have it (see Module access). */
class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $modules = Modules::PATHS[$request->segment(1)] ?? null;

        if ($user && $modules) {
            foreach ($modules as $module) {
                if ($user->can(Modules::permission($module))) {
                    return $next($request);
                }
            }

            $names = implode(' or ', array_map(fn ($m) => Modules::ALL[$m]['name'], $modules));
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, "You don't have access to {$names}.");
            }

            return redirect()->route('dashboard')->with('error', "You don't have access to {$names}. Ask an admin if you need it.");
        }

        return $next($request);
    }
}
