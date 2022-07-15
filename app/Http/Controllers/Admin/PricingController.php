<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceChangeRequest;
use App\Models\Meal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
    public function showProfitValues()
    {
        $mealProfit= DB::table('global_variables')->where('name','meal_profit')->first()->value;
        $kmCost= DB::table('global_variables')->where('name','cost_of_one_km')->first()->value;
        $deliveryPercentage= DB::table('global_variables')->where('name','delivery_profit_percentage')->first()->value;
        $balance= DB::table('global_variables')->where('name','balance')->first()->value;
        return view('admin.accountant_admin.plateform_profit_values',[
            'mealProfit'=>$mealProfit,
            'kmCost'=>$kmCost,
            'deliveryPercentage'=>$deliveryPercentage,
            'balance'=>$balance,
        ]);
    }
    public function editProfitValues(Request $request)
    {
        $this->validate($request, [
            'meal_profit' => 'required|numeric',
            'cost_of_one_km' => 'required|numeric',
            'delivery_profit_percentage' => 'required|numeric',
        ]);
        $mealProfit= $request->meal_profit;
        DB::table('global_variables')->where('name','meal_profit')->update(['value' => $mealProfit]);
        $kmCost=  $request->cost_of_one_km;
        DB::table('global_variables')->where('name','cost_of_one_km')->update(['value' => $kmCost]);
        $deliveryPercentage=$request->delivery_profit_percentage;
        DB::table('global_variables')->where('name','delivery_profit_percentage')->update(['value' => $deliveryPercentage]);
        
        return redirect('/admin/profit-values');
    }
    

}
