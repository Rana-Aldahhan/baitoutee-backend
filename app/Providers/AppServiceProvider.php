<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
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
