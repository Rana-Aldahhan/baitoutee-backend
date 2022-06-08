<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Chef;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function getTopTenAvaialble()
    {
        $subscriptions=Subscription::where('is_available',true)
        ->withCount('meals')
        ->orderBy('starts_at')
        ->take(10)
        ->get();
         //calculate the total price
        $subscriptions->map(function($subscription){
            $subscription->setHidden(['meals_count']);
            $subscription->total_cost=$this->getTotalSubscriptionPrice($subscription);
        });
        return $this->successResponse($subscriptions);
    }
    // the current way to calculate the shown price of a subscription is:
    // delvery fee * subscription days number + meal profit * subscription days number
    //TODO maybe we can cut out meal profits to reduce total cost?
    private function getTotalSubscriptionPrice($subscription){
        return   $subscription->meals_cost
               + $this->getSubscriptionMealsProfit($subscription)
               + $this->getSubscriptionDeliveryFee($subscription);
    }
    private function getSubscriptionMealsProfit($subscription){
        return   $subscription->days_number * $this->getMealProfit();
    }
    private function getSubscriptionDeliveryFee($subscription){
        return   $subscription->days_number * $this->getMealDeliveryFee($subscription->chef_id);
    }
    private function getMealProfit(){
        return DB::table('global_variables')->where('name','meal_profit')->first()->value;
     }
    private function getMealDeliveryFee($chefID){
         //get the user location
         $userLocation=auth('user')->user()->location_id;
         $chef=Chef::find($chefID);
         $distanceBetweenChefAndUser=0;
         if($userLocation==1)//Mazzeh campus
             $distanceBetweenChefAndUser=$chef->location->distance_to_first_location;
         else if($userLocation==2)//Hamak campus
             $distanceBetweenChefAndUser=$chef->location->distance_to_second_location;
         else if ($userLocation==3)//Barzeh campus
             $distanceBetweenChefAndUser=$chef->location->distance_to_third_location;
         $kmCost=DB::table('global_variables')->where('name','cost_of_one_km')->first()->value;
         return $distanceBetweenChefAndUser * $kmCost;
    }
    
}
