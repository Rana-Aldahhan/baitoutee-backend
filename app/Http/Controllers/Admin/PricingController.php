<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chef;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Deliveryman;
use App\Models\PriceChangeRequest;
use App\Models\Meal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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

    public function showChefsFinancialAccounts()
    {
        $chefs=Chef::paginate(10);
        return view('admin.accountant_admin.chefs_financial_accounts',['chefs'=>$chefs]);
    }
    public function searchChefsAccounts(){
        $chefs=Chef::where('name','LIKE','%'.request()->search.'%')
        ->orWhere('id',request()->search)
        ->orWhere('phone_number','LIKE',request()->search.'%')
        ->get()
        ->values();
        $chefs->map(function($chef){
            $chef->unpaid_orders_count=$chef->orders->where('paid_to_chef',false)->count();
        });

        return response()->json($chefs);
    }
    public function showChefFinancialAccount($id)
    {
        $chef=Chef::findOrFail($id);
        $orders=Order::where('chef_id',$id)
        ->where('selected_delivery_time','<',now())
        ->whereIn('status', ['prepared','picked', 'delivered', 'notDelivered','failedAssigning'])
        ->when(request()->paid!=null,function($query){
            return $query->where('paid_to_chef',request()->paid);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('admin.accountant_admin.chef_financial_account',[
            'chef'=>$chef,
            'orders'=>$orders
        ]);
    }
    public function payOrderToChef($id)
    {
        $order=Order::findOrFail($id);
        $order->paid_to_chef=true;
        $order->chef->balance-=$order->meals_cost;
        $order->chef->save();
        $order->save();

        return redirect('/admin/chefs-financial-accounts/'.$order->chef_id);
    }
    //deliverymen
    public function showDeliverymenFinancialAccounts()
    {
        $deliverymen=Deliveryman::paginate(10);
        $deliverymen->map(function($deliveryman){
            $orders_count=0;
            $deliveryman->deliveries->map(function($delivery)use(&$orders_count){
                $delivery->orders->map(function($order)use(&$orders_count){
                    if(!$order->paid_to_accountant)
                        $orders_count+=1;
                });
            });
            $deliveryman->unpaid_orders_count=$orders_count;
        });
        return view('admin.accountant_admin.deliverymen_financial_accounts',['deliverymen'=>$deliverymen]);
    }
    public function searchDeliverymenAccounts(){
        $deliverymen=Deliveryman::where('name','LIKE','%'.request()->search.'%')
        ->orWhere('id',request()->search)
        ->orWhere('phone_number','LIKE',request()->search.'%')
        ->get()
        ->values();
        $deliverymen->map(function($deliveryman){
            $orders_count=0;
            $deliveryman->deliveries->map(function($delivery)use(&$orders_count){
                $delivery->orders->map(function($order)use(&$orders_count){
                    if(!$order->paid_to_accountant)
                        $orders_count+=1;
                });
            });
            $deliveryman->current_balance=$deliveryman->balance;
            $deliveryman->unpaid_orders_count=$orders_count;
            $deliveryman->unpaid_deliveries_count=$deliveryman->deliveries->where('paid_to_deliveryman',false)->count();
        });

        return response()->json($deliverymen);
    }
    public function showDeliverymanFinancialAccount($id)
    {
        $deliveryman=Deliveryman::findOrFail($id);
        $deliveries=Delivery::where('deliveryman_id',$id)
        ->when(request()->paid!=null,function($query){
                return $query->where('paid_to_deliveryman',request()->paid);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $orders=Order::whereIn('delivery_id', $deliveryman->deliveries->pluck('id'))
        ->when(request()->taken!=null,function($query){
            return $query->where('paid_to_accountant',request()->taken);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('admin.accountant_admin.deliveryman_financial_account',[
             'deliveryman'=>$deliveryman,
             'deliveries'=>$deliveries,
             'orders'=>$orders
        ]);
    }
    public function payDeliveryToDeliveryman($id)
    {
        $delivery=Delivery::findOrFail($id);
        $delivery->paid_to_deliveryman=true;
        $delivery->deliveryman->balance-=$delivery->deliveryman_cost_share;
        $delivery->deliveryman->save();
        $delivery->save();

        return redirect('/admin/deliverymen-financial-accounts/'.$delivery->deliveryman_id);
    }
    public function payOrderToAccountant($id)
    {
        $order=Order::findOrFail($id);
        $order->paid_to_accountant=true;
        $order->delivery->deliveryman->total_collected_order_costs-=$order->total_cost;
        $order->delivery->deliveryman->save();
        $order->save();

        return redirect('/admin/deliverymen-financial-accounts/'.$order->delivery->deliveryman_id);
    }

    

}
