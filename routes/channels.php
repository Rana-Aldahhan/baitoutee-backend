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
//channel to broadcast to all deliverymen a new delivery is created so they update their locations
Broadcast::channel('order.deliverymen',function(){
    return (bool)auth('deliveryman')->user()->is_available;
},['guards' => ['deliveryman']]);
//channel to broadcast an event to a deliveryman (e.g: new assigned order)
Broadcast::channel('deliveryman.{id}',function($id){
    return (bool) auth('deliveryman')->user()->id == $id;
},['guards' => ['deliveryman']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
