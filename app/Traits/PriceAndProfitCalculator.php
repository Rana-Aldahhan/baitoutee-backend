<?php

namespace App\Traits;
use App\Models\Chef;
use Illuminate\Support\Facades\DB;


trait PriceAndProfitCalculator{

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
}