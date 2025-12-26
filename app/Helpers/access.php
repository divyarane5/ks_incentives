<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        return Auth::check() && Auth::user()->hasRole(config('roles.SUPER_ADMIN'));
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return Auth::check() && Auth::user()->hasRole(config('roles.ADMIN'));
    }
}

/**
 * Permission + Business Unit check
 */
if (!function_exists('canAccessModule')) {
    function canAccessModule(string $permission, array $businessUnits = [])
    {
        if (!Auth::check()) return false;

        $user = Auth::user();

        // 🔥 Super Admin bypass
        if (isSuperAdmin()) return true;

        // 👑 Admin sees everything
        if (isAdmin()) return true;

        // ❌ No permission
        if (!$user->can($permission)) return false;

        // ✅ No BU restriction
        if (empty($businessUnits)) return true;

        // ✅ Business unit match (compare code)
        return in_array(optional($user->businessUnit)->code, $businessUnits);
    }

}
