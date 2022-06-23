<?php

namespace App\Traits;
use App\Models\Chef;
use App\Models\Order;
use App\Models\Meal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


trait MealsHelper{

    protected function getMealProfit(){
        return DB::table('global_variables')->where('name','meal_profit')->first()->value;
     }

    protected function getMealDeliveryFee($chefID){
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
         else $distanceBetweenChefAndUser=0;
         $kmCost=DB::table('global_variables')->where('name','cost_of_one_km')->first()->value;
         return $distanceBetweenChefAndUser * $kmCost;
    }
    protected function getDeliveryFeeFromUserTochef($chef,$user){
                 //get the user location
                 $userLocation=$user->location_id;
                 $distanceBetweenChefAndUser=0;
                 if($userLocation==1)//Mazzeh campus
                     $distanceBetweenChefAndUser=$chef->location->distance_to_first_location;
                 else if($userLocation==2)//Hamak campus
                     $distanceBetweenChefAndUser=$chef->location->distance_to_second_location;
                 else if ($userLocation==3)//Barzeh campus
                     $distanceBetweenChefAndUser=$chef->location->distance_to_third_location;
                 else $distanceBetweenChefAndUser=0;
                 $kmCost=DB::table('global_variables')->where('name','cost_of_one_km')->first()->value;
                 return $distanceBetweenChefAndUser * $kmCost;
    }
    public function getCountOfTodayAssingedTotalMeals(Chef $chef){
        $mealsCount=0;
        $todayOrders=Order::with('meals')
        ->where('chef_id',$chef->id)
        ->where('subscription_id',null)//don't count the subscription meals
        ->where('status','!=','pending')//take only the assigned meals to chef 
        ->where('status','!=','not approved')
        ->where('status','!=','canceled')
        ->whereDate('selected_delivery_time',Carbon::today())//take the orders of today
        ->get();
        $todayOrders->map(function ($order) use (&$mealsCount){
            $order->meals->map(function ($meal) use (&$mealsCount){
                 $mealsCount+=$meal->pivot->quantity ;
            });
        });
        return $mealsCount;
    }

    public function getCountOfTodayAssingedMeals(Chef $chef,Meal $meal){
        $mealsCount=0;
        $todayOrders=Order::with('meals')
        ->where('chef_id',$chef->id)
        ->where('subscription_id',null)//don't count the subscription meals
        ->where('status','!=','pending')//take only the assigned meals to chef 
        ->where('status','!=','not approved')
        ->where('status','!=','canceled')
        ->whereDate('selected_delivery_time',Carbon::today())//take the orders of today
        ->get()
        ->map(function ($order) use (&$mealsCount,$meal){
            $order->meals->map(function ($orderMeal) use (&$mealsCount,$meal){
                if($meal->id == $orderMeal->id)
                    $mealsCount+=$orderMeal->pivot->quantity ;
            });
        });
        return $mealsCount;
    }
    public function getCountOfTommorowAssingedTotalMeals(Chef $chef){
        $mealsCount=0;
        $todayOrders=Order::with('meals')
        ->where('chef_id',$chef->id)
        ->where('subscription_id',null)//don't count the subscription meals
        ->where('status','!=','pending')//take only the assigned meals to chef 
        ->where('status','!=','not approved')
        ->where('status','!=','canceled')
        ->whereDate('selected_delivery_time',Carbon::tomorrow())//take the orders of tomorrow
        ->get();
        $todayOrders->map(function ($order) use (&$mealsCount){
            $order->meals->map(function ($meal) use (&$mealsCount){
                 $mealsCount+=$meal->pivot->quantity ;
            });
        });
        return $mealsCount;
    }

    public function getCountOfTomorrowAssingedMeals(Chef $chef,Meal $meal){
        $mealsCount=0;
        $todayOrders=Order::with('meals')
        ->where('chef_id',$chef->id)
        ->where('subscription_id',null)//don't count the subscription meals
        ->where('status','!=','pending')//take only the assigned meals to chef 
        ->where('status','!=','not approved')
        ->where('status','!=','canceled')
        ->whereDate('selected_delivery_time',Carbon::tomorrow())//take the orders of tomorrow
        ->get()
        ->map(function ($order) use (&$mealsCount,$meal){
            $order->meals->map(function ($orderMeal) use (&$mealsCount,$meal){
                if($meal->id == $orderMeal->id)
                    $mealsCount+=$orderMeal->pivot->quantity ;
            });
        });
        return $mealsCount;
    }

}