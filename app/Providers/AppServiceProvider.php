<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
        Gate::before(function ($user) {
            if ($user->role->name=='super admin') {
                return true;
            }
        });
        Gate::define('add-admins', function ($user) {
            return $user->role->name=='super admin';
        });
        Gate::define('approve-reject-join-requests', function ($user) {
            return $user->role->name === 'hr admin' ;
        });
        Gate::define('approve-reject-meal-prices', function ($user) {
            return $user->role->name === 'accountant admin' ;
        });
        Gate::define('manage-orders', function ($user) {
            return $user->role->name === 'orders admin' ;
        });

    }
}
