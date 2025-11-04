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
        // Share business unit with all views
        View::composer('*', function ($view) {
            $unit = null;

            if (Auth::check()) {
                $user = Auth::user();
                $unit = $user->businessUnit; // assuming relation exists
            }

            if (!$unit) {
                // default unit (for guest or fallback)
                $unit = BusinessUnit::first();
            }

            $view->with('activeBusinessUnit', $unit);
        });
    }
}
