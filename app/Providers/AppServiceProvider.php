<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessUnit; // ✅ add this

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // `php artisan serve` hides the Windows temp folder from PHP, so file uploads fail
        // on a laptop. Pass it through. (Has no effect on the real server.)
        array_push(\Illuminate\Foundation\Console\ServeCommand::$passthroughVariables, 'TEMP', 'TMP', 'TMPDIR');

        // KeyLeads CSS/JS (public/build) always load from this site, even when ASSET_URL
        // in .env points the old back-office assets somewhere else.
        \Illuminate\Support\Facades\Vite::createAssetPathsUsing(fn ($path, $secure = null) => url($path));

        // Share business unit with all views
        View::composer('*', function ($view) {
            // KeyLeads screens don't use it, and they render many small views per page.
            if (str_starts_with($view->getName(), 'crm.')) {
                return;
            }
            static $resolved = false, $cached = null;
            if ($resolved) {
                $view->with('activeBusinessUnit', $cached);
                return;
            }
            $unit = null;

            if (Auth::check()) {
                $user = Auth::user();
                $unit = $user->businessUnit; // assuming relation exists
            }

            if (!$unit) {
                // default unit (for guest or fallback)
                $unit = BusinessUnit::first();
            }

            $resolved = true;
            $cached = $unit;
            $view->with('activeBusinessUnit', $unit);
        });
    }
}
