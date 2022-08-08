<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Report;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function getCurrentOrders()
    {
        $user=auth('user')->user();
        $orders=$user->orders()
        ->whereDate('selected_delivery_time',Carbon::today())
        ->orWhereDate('selected_delivery_time',Carbon::tomorrow())//get the preordered or tomorrow subscription order
        ->where('user_id',$user->id)
        ->get();
        
        $orders=$orders->map(function($order){
            $order->can_be_canceled=($order->status=='pending')||($order->selected_delivery_time >=Carbon::tomorrow() && $order->status!='canceled');
            if($order->supscription_id!=null){
                if($order->total_cost==0)
                    $order->can_be_canceled=true;
                else
                    $order->can_be_canceled=false;
            }
            $order->chef_name=$order->chef->name;
            $order->chef_image=$order->chef->profile_picture;
            return $order->only(['id','can_be_canceled','status','chef_name','chef_image','selected_delivery_time','created_at','subscription_id']);
        });

        return $this->successResponse($orders);
    }
    public function cancelOrder(Order $order){
        if((($order->status!='pending')&&($order->selected_delivery_time <Carbon::tomorrow()) )|| $order->status=='canceled')
        {
            return $this->errorResponse('لا يمكن إلغاء هذا الطلب',400);
        }
        else{
            if($order->subscription_id!=null && $order->total_cost!=0 ){
                return $this->errorResponse('لا يمكن إلغاء هذا الطلب',400);
            }
            $order->status='canceled';
            $order->save();
            return $this->successResponse(['message'=>'order has been canceled successfully'],200);
        }

    }
    public function getOrdersHistory(){
        $user=auth('user')->user();
        $orders=$user->orders()
        ->whereDate('selected_delivery_time','<',Carbon::today())
        ->paginate(10);
        
        $orders->map(function($order){
            $order->can_be_canceled=($order->status=='pending')||($order->selected_delivery_time >=Carbon::tomorrow());
            $order->chef_name=$order->chef->name;
            $order->chef_image=$order->chef->profile_picture;
            $order->setHidden(['user_id','chef_id','delivery_id','notes','total_cost','meals_cost','profit','accepted_at','prepared_at','paid_to_chef','paid_to_accountant','updated_at','chef']);
        });
        
        return $this->paginatedResponse($orders);
    }
    public function showOrder(Order $order){
        $order->chef_name=$order->chef->name;
        $order->delivery_fee=$order->total_cost-($order->meals_cost + $order->profit );
        $order->can_be_canceled=($order->status=='pending')||($order->selected_delivery_time >=Carbon::tomorrow() && $order->status!='canceled');
        $order->meals_count=0;
        $order->meals->map(function($meal)use($order){
            $order->meals_count+=$meal->pivot->quantity;
        });
        if($order->subscription_id!=null)
            {
                if($order->total_cost==0)
                    $order->can_be_canceled=true;
                else
                    $order->can_be_canceled=false;
                $order->total_cost='-';
                $order->delivery_fee='-';
                
            }
        $order->can_be_evaluated=$order->status=='delivered';
        $order->meals=$order->meals->map(function($meal)use($order){
            $meal->quantity=$meal->pivot->quantity;
            $meal->user_rate=$meal->pivot->meal_rate;
            if($order->subscription_id!=null)
                $meal->price='-';
            else
               {
                 $meal->price+=$order->profit/ $order->meals_count;
                 if($meal->discount_percentage!=null)
                     $meal->price=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100));
                }
            $meal=$meal->only(['id','name','image','price','quantity','user_rate']);
            return $meal;
        });
        $order=$order->only(['id','chef_name','selected_delivery_time','can_be_canceled','status','can_be_evaluated',
        'subscription_id','created_at','notes','delivery_fee','total_cost','meals']);

        return $this->successResponse($order);
    }
    public function rateOrder(Request $request,Order $order){
        if($order->status!='delivered')
            return $this->errorResponse('لا يمكن تقييم طلب غير مكتمل بعد',400);
        $validator = Validator::make($request->all(), [
            'meals'=>'array',
            'meals.*.id' => 'required',
            'meals.*.rate' => ['required',Rule::in([1,2,3,4,5])]
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        foreach($request->meals as $ratedMeal){
            $meal=$order->meals->find($ratedMeal['id']);
            $meal->pivot->meal_rate=$ratedMeal['rate'];
            $meal->pivot->meal_rate_notes=$ratedMeal['notes'];
            $meal->pivot->save();
            $meal->rating= ( ($meal->rating*$meal->rates_count) + $ratedMeal['rate'] )/($meal->rates_count+1);
            $meal->rates_count+=1;
            $meal->save();
        }

        return $this->successResponse(['message'=>'rates sent successfully']);
    }
    public function reportOrder(Request $request,Order $order){
        if($order->status!='delivered')
            return $this->errorResponse('لا يمكن الإبلاغ عن طلب لم يتم إكماله بعد',400);
        $validator = Validator::make($request->all(), [
            'reported_on' =>  ['required', Rule::in(['delivery','chef'])],
            'reason'=>'required'
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $report=new Report();
        $report->order()->associate($order);
        $report->sendable()->associate(auth('user')->user());
        if($request->reported_on=='delivery')
           {
             $report->receivable()->associate($order->delivery->deliveryman);
           }
        else if ($request->reported_on=='chef') 
            $report->receivable()->associate($order->chef);
        $report->reason=$request->reason;
        $report->save();
        return $this->successResponse(['message'=>'report sent successfully'],201);
    }
    public function getCurrentSubscriptions()
    {
        $user=auth('user')->user();
        $subscriptions=$user->subscriptions->filter(function($subscription){
            return Carbon::create($subscription->starts_at)->addDays($subscription->days_number)>=Carbon::today();
        })
        ->values();
        $subscriptions->map(function($subscription){
            $subscription->ends_at= Carbon::create($subscription->starts_at)->addDays($subscription->days_number);
            $subscription->makeHidden(['pivot','chef_id','is_available']);
        });
        return $this->successResponse($subscriptions);
    }
    public function getSubscriptionOrders(Subscription $subscription)
    {
       $orders=$subscription->orders->filter(function($order){
        return Carbon::create($order->selected_delivery_time)>=Carbon::today() && $order->status!='canceled';
        })
        ->values()
        ->map(function($order){
            $order->can_be_canceled=($order->selected_delivery_time >=Carbon::tomorrow() && $order->status!='canceled'&& $order->total_cost==0);//TODO can't canel first order
            $order->meal_name=$order->meals->first()->name;
            $order->meal_image=$order->meals->first()->image;
            return $order->only(['id','selected_delivery_time','meal_name','meal_image','can_be_canceled']);
        });

        return $this->successResponse($orders);
    }
    public function getProfile()
    {
        $user=auth('user')->user();
        $userInfo['email']=$user->email;
        $userInfo['name']=$user->name;
        $userInfo['phone_number']=$user->phone_number;
        $userInfo['campus_card_expiry_date']=$user->campus_card_expiry_date;

        return $this->successResponse($userInfo);
    }
    public function getSavedList()
    {
        $user=auth('user')->user();
        $savedMeals=$user->savedMeals;
        $savedMeals=$savedMeals->map(function($meal){
            $meal->chef_name=$meal->chef->name;
            return $meal->only(['id','name','chef_name','image']);
        });

        return $this->successResponse($savedMeals);
    }

}
