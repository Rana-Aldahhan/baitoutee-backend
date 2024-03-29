<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Services\FCMService;

class DeliverymanController extends Controller
{
    public function updateCurrentLocation(Request $request)
    {
        $deliveryman=auth('deliveryman')->user();
        $validator = Validator::make($request->all(), [
            'current_longitude' => 'required',
            'current_latitude'=> 'required',
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $deliveryman->update([
            'current_longitude'=>$request->current_longitude,
            'current_latitude'=>$request->current_latitude,
            'updated_at'=>now()
        ]);
        return $this->successResponse([],200);
    }
    public function getCurrentDeliveryInfoAndOrders()
    {
        $deliveryman=auth('deliveryman')->user();
        $delivery=$deliveryman->deliveries()
        ->with('orders')
        ->whereDate('created_at',Carbon::today())
        ->where('delivered_at',null)
        ->first();
        if($delivery!=null){
            $delivery->chef_name=$delivery->orders->first()->chef->name;
            $delivery->chef_location=$delivery->orders->first()->chef->location->only(['id','latitude','longitude','name']);
            $delivery->chef_phone_number=$delivery->orders->first()->chef->phone_number;
            $delivery->destination=$delivery->orders->first()->user->location->name;
            $delivery->selected_delivery_time=$delivery->orders->first()->selected_delivery_time;
            $delivery->total_meal_count=0;
            $delivery->orders->map(function($order)use(&$delivery){
                $order->meals->map(function($meal)use(&$delivery){
                    $delivery->total_meal_count+=$meal->pivot->quantity;
                });
            });
            $delivery->total_cost=0;
            $delivery->orders->map(function($order)use(&$delivery){
                $delivery->total_cost+=$order->total_cost;
            });
            $delivery=$delivery->only(['id','chef_name','chef_phone_number','chef_location','destination','selected_delivery_time','total_meal_count','total_cost','orders']);
            $delivery['orders']=$delivery['orders']->map(function ($order){
                $order->user_name=$order->user->name;
                $order->user_phone_number=$order->user->phone_number;
                $order->meals_count=0;
                $order->meals->map(function($meal)use($order){
                    $order->meals_count+=$meal->pivot->quantity;
                });
                $order->has_notes=$order->notes!=null;
                $order->meals->map(function($meal)use($order){
                    if($meal->pivot->notes != null)
                        $order->has_notes=true;
                });
                return $order->only(['id','user_name','user_phone_number',
                'meals_count','total_cost','has_notes']);
            });
        }

        return $this->successResponse($delivery);

    }
    public function getOrderForDelivery(Order $order)
    {
        $order->user_name=$order->user->name;
        $order->user_location=$order->user->location->name;
        $order->user_phone_number=$order->user->phone_number;
        $order->meals_count=0;
        $order->meals->map(function($meal)use($order){
            $order->meals_count+=$meal->pivot->quantity;
        });
        $order= $order->only(['id','status','user_name','user_phone_number',
        'meals_count','total_cost','meals','profit']);
        $order['meals']= $order['meals']->map(function($meal)use ($order){
            $meal->price+=$order['profit']/ $order['meals_count'];
            $meal->notes=$meal->pivot->notes;
            $meal->quantity=$meal->pivot->quantity;
            return $meal->only(['id','name','price','notes','quantity']);
        });
       unset($order['profit']);
        return $this->successResponse($order);
    }
    public function changeOrderStatus(Request $request,Order $order)
    {
        $validator = Validator::make($request->all(), [
            'new_status' =>  ['required', Rule::in(['picked','delivered','notDelivered'])],
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        if($request->new_status=='picked')
        {
            $order->status=$request->new_status;
            $order->delivery->picked_at=now();
            $order->save();
            $order->delivery->save();
            //send notification of order status change to user
            $user=$order->user;
            FCMService::sendPushNotification(
                $user->fcm_token,
                'طلبك جاري توصيله',
               ' يتم الآن توصيل طلبك ذو الرقم ' .$order->id
            );
        }
        else{
            $order->status=$request->new_status;
            $order->delivery->delivered_at=now();
            $order->save();
            $order->delivery->save();
           //add  order delivery cost to deliveryman balance
            $deliveryProfitPercentage= DB::table('global_variables')->where('name','delivery_profit_percentage')->first()->value;
            $deliveryman=auth('deliveryman')->user();
            // $deliveryman->balance+=($order->delivery->cost*$deliveryProfitPercentage)/100;
            $deliveryman->balance+=$order->delivery->deliveryman_cost_share;
            $deliveryman->total_collected_order_costs+=$order->total_cost;
            //set the deliveryman status to available again
            $deliveryman->save();
        }
        return $this->successResponse(['message'=>'status of order changed successfully']);
    }
    public function reportOrder(Request $request,Order $order)
    {
        $validator = Validator::make($request->all(), [
            'reported_on' =>  ['required', Rule::in(['user','chef'])],
            'reason'=>'required'
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $report=new Report();
        $report->order()->associate($order);
        $report->sendable()->associate(auth('deliveryman')->user());
        if($request->reported_on=='user')
           {
             $report->receivable()->associate($order->user);
           }
        else if ($request->reported_on=='chef')
            $report->receivable()->associate($order->chef);
        $report->reason=$request->reason;
        $report->save();
        return $this->successResponse(['message'=>'report sent successfully'],201);
    }

    // get details of deliveryMan balance (today- this week - this month)
    public function getBalance ()
    {
        $deliveryman=auth('deliveryman')->user();
        $todayBalance = 0; $todayBalanceReceived =0;
        $thisWeekBalance =0; $thisWeekBalanceReceived =0;
        $thisMonthBalance =0;  $thisMonthBalanceReceived =0;
        $todayOrders =0; $thisWeekOrders=0; $thisMonthOrders =0;

        // map throw orders and deliveries to get the balance - what recieved - orders count for delivery man
        $deliveryman->deliveries()->whereNotNull('delivered_at')->get()->map(function($delivery)
        use (&$todayBalance,&$thisWeekBalance,&$thisMonthBalance,
            &$todayBalanceReceived,&$thisWeekBalanceReceived,&$thisMonthBalanceReceived,
            &$todayOrders,&$thisWeekOrders,&$thisMonthOrders){
                $deliveryCost =(!$delivery->paid_to_deliveryman)?($delivery->deliveryman_cost_share):0;
                // $deliveryRecievedCost =  (!$delivery->paid_to_deliveryman)?($delivery->delivaryman_cost_share):0;
                $deliveredOrders = $delivery->orders()->get()->count();

            if($delivery->delivered_at->isSameDay()){
                $todayBalance+= $deliveryCost;
                // $todayBalanceReceived+= $deliveryRecievedCost;
                $todayOrders+= $deliveredOrders;
            }
            //Note: that the week start from monday not sunday
            if($delivery->delivered_at->isSameWeek()){
                $thisWeekBalance+= $deliveryCost;
                // $thisWeekBalanceReceived+= $deliveryRecievedCost;
                $thisWeekOrders+= $deliveredOrders;
            }
            if($delivery->delivered_at->isCurrentMonth()){
                $thisMonthBalance+= $deliveryCost;
                // $thisMonthBalanceReceived+= $deliveryRecievedCost;
                $thisMonthOrders+= $deliveredOrders;
            }
            $delivery->orders->map(function($order)use( &$todayBalanceReceived,&$thisWeekBalanceReceived,&$thisMonthBalanceReceived,&$deliveryRecievedCost){
                $deliveryRecievedCost += (!$order->paid_to_Accountant)?($order->total_cost):0;

            if(Carbon::create($order->selected_delivery_time)->isSameDay()){
                $todayBalanceReceived+= $deliveryRecievedCost;
            }
            //Note: that the week start from monday not sunday
            if(Carbon::create($order->selected_delivery_time)->isSameWeek()){
                $thisWeekBalanceReceived+= $deliveryRecievedCost;
            }
            if(Carbon::create($order->selected_delivery_time)->isCurrentMonth()){
                $thisMonthBalanceReceived+= $deliveryRecievedCost;
            }
            });
        });
        // today balance
        $today = collect([
            'balance'=> $todayBalance,
            'recieved' =>$todayBalanceReceived,
            'orders_count'=>$todayOrders
        ]);
        // this week balance
        $thisWeek = collect([
            'balance'=> $thisWeekBalance,
            'recieved' =>$thisWeekBalanceReceived,
            'orders_count'=>$thisWeekOrders
        ]);
        // this month balance
        $thisMonth = collect([
            'balance'=> $thisMonthBalance,
            'recieved' =>$thisMonthBalanceReceived,
            'orders_count'=>$thisMonthOrders
        ]);
        // return today-this week-this month balance
        return $this->successResponse(collect(
            ["balance"=>$deliveryman->balance,
             "today"=>$today,
             "this_week"=>$thisWeek,
             "this_month"=>$thisMonth]
        ));
    }

    public function changeAvailabilityStatus()
    {
        $deliveryman=auth('deliveryman')->user();
        $deliveryman->is_available=!$deliveryman->is_available;
        $deliveryman->save();

        return $this->successResponse([]);
    }
}
