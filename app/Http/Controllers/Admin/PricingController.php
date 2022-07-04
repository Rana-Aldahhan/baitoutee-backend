<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceChangeRequest;
use App\Models\Meal;
use Illuminate\Support\Facades\Gate;


class PricingController extends Controller
{
   public function approveMeal($id) {
    \Auth::shouldUse('backpack');
    Gate::authorize('approve-reject-meal-prices');
       $meal=Meal::find($id);
       $meal->approved=true;
       $meal->save();
       return redirect('/admin/meal');
   }
   public function rejectMeal($id) {
    \Auth::shouldUse('backpack');
    Gate::authorize('approve-reject-meal-prices');
    $meal=Meal::find($id);
    $meal->approved=false;
    $meal->save();
    return redirect('/admin/meal');
    }
    public function approvePriceChangeRequest($id) {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-meal-prices');
           $priceChangeRequest=PriceChangeRequest::find($id);
           $priceChangeRequest->approved=true;
           $priceChangeRequest->save();
           $meal=Meal::find($priceChangeRequest->meal_id);
           $meal->price=$priceChangeRequest->new_price;
           $meal->save();
           return redirect('/admin/price-change-request');
       }
       public function rejectPriceChangeRequest($id) {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-meal-prices');
        $priceChangeRequest=PriceChangeRequest::find($id);
        $priceChangeRequest->approved=false;
        $priceChangeRequest->save();
        return redirect('/admin/price-change-request');
        }
    

}
