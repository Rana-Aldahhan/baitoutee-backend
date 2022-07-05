<?php

use Illuminate\Support\Facades\Broadcast;
use PhpParser\Node\Expr\Cast\Bool_;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('test.channel',function(){
    return true;
}, ['guards' => ['deliveryman']]);
Broadcast::channel('order.deliverymen',function(){
    return (bool)auth('deliveryman')->user()->is_available;
},['guards' => ['deliveryman']]);
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
