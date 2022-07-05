<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\Deliveryman;
use App\Models\Location;
use App\Traits\DistanceCalculator;
use App\Traits\MealsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignOrderToDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DistanceCalculator, MealsHelper;

    public $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(10);
        $availableDeliverymen=Deliveryman::where('is_available',true)
        ->where('updated_at','>=',now()->subMinute())
        ->get();
        if($availableDeliverymen->count()>0){
            $chef=$this->order->chef;
            $chefLocation=$chef->location;
            //calculate distance
            $availableDeliverymen=$availableDeliverymen
             //sort objects
            ->sortBy(function ($deliveryman)use ($chefLocation){
                $deliverymanLocation= new Location();
                $deliverymanLocation->latitude=$deliveryman->current_latitude;
                $deliverymanLocation->longitude=$deliveryman->current_longitude;
                $deliverymanLocation->name="current location";
                $distance=$this->calculateDistanceBetweenTwoPoints($chefLocation,$deliverymanLocation);
                return $distance;
            });
            //make new Delivery and assign it to first deliveryman
            $user = $this->order->user;
            $deliveryCost = $this->getDeliveryFeeFromUserTochef($chef, $user);
            $assignedDeliveryman = $availableDeliverymen->first();
            $delivery = Delivery::create([
                'deliveryman_id' => $assignedDeliveryman->id,
                'cost' => $deliveryCost,
            ]);
            $this->order->delivery()->associate($delivery);
            $this->order->save();
            $assignedDeliveryman->is_available = false;
            $assignedDeliveryman->save();
        } else {
            $this->order->status = 'failedِAssigning';
            $this->order->save();
        }

    }
}
