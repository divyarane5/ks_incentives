<?php

namespace App\Providers;

use App\Models\IndentConfiguration;
use App\Models\User;
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

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });

        Gate::define('indent-approval', function($user) {
            $userId = auth()->user()->id;
            $indentConfigurationCount = IndentConfiguration::whereRaw('FIND_IN_SET("'.$userId.'", approver1)')
                                        ->orWhereRaw('FIND_IN_SET("'.$userId.'", approver2)')
                                        ->orWhereRaw('FIND_IN_SET("'.$userId.'", approver3)')
                                        ->orWhereRaw('FIND_IN_SET("'.$userId.'", approver4)')
                                        ->orWhereRaw('FIND_IN_SET("'.$userId.'", approver5)')
                                        ->count();
            return (($indentConfigurationCount > 0) || auth()->user()->hasRole('Superadmin'));
        });

        Gate::define('reimbursement-approval', function($user) {
            $userId = auth()->user()->id;
            $asParentCount = User::where('reporting_manager_id', $userId)
                                        ->count();
            return ($asParentCount > 0 || auth()->user()->hasRole('Superadmin'));
        });
    }
}
