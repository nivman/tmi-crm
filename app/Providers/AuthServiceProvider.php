<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\User::class => \App\Policies\UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            if ($user->hasRole('super')) {
                return true;
            }
        });
        Gate::define('update', 'App\Policies\UserPolicy@update');
        Gate::define('delete', 'App\Policies\UserPolicy@delete');
    }
}
