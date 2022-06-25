<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Broadcast::routes(['middleware' => ['auth:deliveryman']]);
        // Broadcast::channel('test.channel',function(){
        //     return true;
        // }, ['guards' => ['deliveryman']]);
        require base_path('routes/channels.php');
    }
}
