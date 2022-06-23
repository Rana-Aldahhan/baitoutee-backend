<?php

namespace App\Listeners;

use App\Events\OrderIsPrepared;
use App\Jobs\AssignOrderToDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderIsPreparedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($order)
    {
       
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderIsPrepared  $event
     * @return void
     */
    public function handle(OrderIsPrepared $event)
    {
        AssignOrderToDelivery::dispatch($event->order);
    }
}
