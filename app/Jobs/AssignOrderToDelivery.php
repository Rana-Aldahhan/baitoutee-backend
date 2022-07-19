<?php

namespace App\Jobs;

use App\Events\DeliveryIsAssigned;
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
use Illuminate\Support\Facades\DB;
use App\Services\FCMService;

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
            if($this->order->subscription_id!=null)//case of a subscription order take delivery cost from pivot table
            {
                $deliveryCost=$this->order->subscription->users()->find($this->order->user_id)->pivot->delivery_cost_per_day;
            } else {
                $deliveryCost = $this->order->total_cost-($this->order->meals_cost+$this->order->profit);
            }
            $assignedDeliveryman = $availableDeliverymen->first();
            $deliveryProfitPercentage= DB::table('global_variables')->where('name','delivery_profit_percentage')->first()->value;
            $delivery = Delivery::create([
                'deliveryman_id' => $assignedDeliveryman->id,
                'cost' => $deliveryCost,
                'deliveryman_cost_share'=>($deliveryCost*$deliveryProfitPercentage)/100
            ]);
            $this->order->delivery()->associate($delivery);
            $this->order->save();
            $assignedDeliveryman->is_available = false;
            $assignedDeliveryman->save();
            //send notification to assigned deliveryman
            FCMService::sendPushNotification(
                $assignedDeliveryman->fcm_token,
                'طلب توصيل جديد ',
                $delivery->id.' لقد تم إسناد طلب توصيل جديد إليك، توصيل  رقم'
            );
            //broadcast event to deliveryman so he knows a new delivery has been assigned to him
            broadcast(new DeliveryIsAssigned($assignedDeliveryman));
        } else {
            $this->order->status = 'failedِAssigning';
            $this->order->save();
        }

    }
}
