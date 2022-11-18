<?php

namespace App\Providers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ExpenseRepositoryInterface;
use App\Interfaces\IndentConfigurationRepositoryInterface;
use App\Interfaces\IndentRepositoryInterface;
use App\Interfaces\ReimbursementRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\ClientRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\IndentConfigurationRepository;
use App\Repositories\IndentRepository;
use App\Repositories\ReimbursementRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(IndentConfigurationRepositoryInterface::class, IndentConfigurationRepository::class);
        $this->app->bind(IndentRepositoryInterface::class, IndentRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(ReimbursementRepositoryInterface::class, ReimbursementRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
