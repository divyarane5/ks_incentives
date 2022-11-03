<?php

namespace App\Providers;

use App\Models\IndentConfiguration;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('indent-approval', function($user) {
            $userId = auth()->user()->id;
            $indentConfigurationCount = IndentConfiguration::where('approver1', $userId)
                                        ->orWhere('approver2', $userId)
                                        ->orWhere('approver3', $userId)
                                        ->orWhere('approver4', $userId)
                                        ->orWhere('approver5', $userId)
                                        ->count();
            return $indentConfigurationCount > 0;
        });
    }
}
