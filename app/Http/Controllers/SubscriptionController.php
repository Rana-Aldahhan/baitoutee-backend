<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Traits\PriceAndProfitCalculator;

class SubscriptionController extends Controller
{
    use PriceAndProfitCalculator;
    
    public function getTopTenAvaialble()
    {
        $subscriptions=Subscription::where('is_available',true)
        ->orderBy('starts_at')
        ->take(10)
        ->get();
         //calculate the total price
        $subscriptions->map(function($subscription){
            $subscription->total_cost=$this->getTotalSubscriptionPrice($subscription);
        });
        return $this->successResponse($subscriptions);
    }
    public function getAllAvaialble()
    {
        $subscriptionsPaginated=Subscription::where('is_available',true)
        ->orderBy('starts_at')
        ->paginate(10);
         //calculate the total price
        $subscriptionsPaginated->map(function($subscription){
            $subscription->total_cost=$this->getTotalSubscriptionPrice($subscription);
        });
        return $this->paginatedResponse($subscriptionsPaginated);
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
    
    
}
