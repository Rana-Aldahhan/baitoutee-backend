<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Order;
use App\Models\Chef;
use Illuminate\Http\Request;
use App\Traits\MealsHelper;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    use MealsHelper;
    
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
    public function show(Subscription $subscription){
        if($subscription->exists)
            {
                $subscription->total_cost=$this->getTotalSubscriptionPrice($subscription);
                $subscription->rating=$this->getsubscriptionRating($subscription);
                $subscription->rating_count=$this->getsubscriptionRatingCount($subscription);
                $subscription->available_subscriptions_count=$subscription->max_subscribers-$this->getCurrentSubscribersCount($subscription);
                $subscription->has_subscribed=auth('user')->user()->subscriptions->where('id',$subscription->id)->count()>0;
                $subscription->setHidden(['meals','users']);
                return $this->successResponse($subscription);
            }
    }
    public function showMeals(Subscription $subscription)
    {
        $meals=$subscription->meals()->get(['meal_id','name','price','image','rating','rates_count']);
        $meals->map(function ($meal) use($subscription){
            $meal->day_number=$meal->pivot->day_number;
            $meal->meal_date=Carbon::create($subscription->starts_at)->addDays($meal->day_number)->setTimeFromTimeString($subscription->meal_delivery_time)->toDateTimeString();
            $meal->setHidden(['pivot','chef','category']);
        })
        ->sortBy('day_number');
        return $this->successResponse($meals);
    }
    public function getChefSubscriptions(Chef $chef){
        $subscriptions=$chef->subscriptions()->where('is_available',true)->orderBy('starts_at')->get();
        $subscriptions->map(function($subscription){
            $subscription->total_cost=$this->getTotalSubscriptionPrice($subscription);
            $subscription->rating=$this->getsubscriptionRating($subscription);
            $subscription->rating_count=$this->getsubscriptionRatingCount($subscription);
            $subscription->available_subscriptions_count=$subscription->max_subscribers-$this->getCurrentSubscribersCount($subscription);
            $subscription->has_subscribed=auth('user')->user()->subscriptions->where('id',$subscription->id)->count()>0;
            $subscription->setHidden(['meals','users','chef','created_at','updated_at','max_subscribers','meals_cost']);
        });
        return $this->successResponse($subscriptions);
    }
    private function getCurrentSubscribersCount(Subscription $subscription){
            return $subscription->users->count();
    }
    private function getsubscriptionRating(Subscription $subscription)
    {
        $avgRating=null;
        $ratedMealsCount=0;
        $subscription->meals->map(function($meal) use (&$avgRating,&$ratedMealsCount){
            if($meal->rating!=null)
               {
                $avgRating+=$meal->rating;
                $ratedMealsCount+=1;
               }
        });
        if($avgRating!=null)
            $avgRating=$avgRating/$ratedMealsCount;
        return $avgRating;
    }
    private function getsubscriptionRatingCount(Subscription $subscription)
    {
        $ratesCount=0;
        $subscription->meals->map(function($meal) use (&$ratesCount){
            $ratesCount+=$meal->rates_count;
        });
        return $ratesCount;
    }
    public function subscribe(Subscription $subscription,Request $request)
    {
        if($subscription->starts_at<now())
         return $this->errorResponse("عذراً لم يعد التسجيل على هذا الاشتراك متاحاً",400);
        if(!$subscription->is_available)
         return $this->errorResponse("عذراً لم يعد التسجيل على هذا الاشتراك متاحاً",400);
        if(auth('user')->user()->subscriptions->where('id',$subscription->id)->count()>0)
            return $this->errorResponse("أنت مشترك ضمن هذا الاشتراك مسبقاً",400);
        if($subscription->max_subscribers < $this->getCurrentSubscribersCount($subscription)+1)
            return $this->errorResponse("لقد وصل الاشتراك إلى العدد الأقصى من المشتركين",400);
        auth('user')->user()->subscriptions()->attach($subscription->id,[
            'notes'=>$request->notes,
            'total_cost'=>$this->getTotalSubscriptionPrice($subscription),
            'delivery_cost_per_day'=>$this->getMealDeliveryFee($subscription->chef->id)
        ]);
        //make orders for each day
        $this->addSubscriptionOrdersToUser($subscription,$request);
        
        return $this->successResponse(['message'=>'subscribed successfully'],201);
        
    }
    public function addSubscriptionOrdersToUser(Subscription $subscription,Request $request)
    {
        $subscription->meals->map(function($meal)use ($subscription,$request){
            //if it is the first order of the subscription we will store pricing info: the total price,profits,meal prices 
            //if it is not then we will won't store any pricing information
            $totalCost=0;
            $profits=0;
            $mealsCost=0;
            $meal->pivot->day_number==1?$totalCost=$this->getTotalSubscriptionPrice($subscription):$totalCost=0;
            $meal->pivot->day_number==1?$profits=$this->getSubscriptionMealsProfit($subscription):$profits=0;
            $meal->pivot->day_number==1?$mealsCost=$subscription->meals_cost:$mealsCost=0;
            $order=Order::create([
                'user_id'=>auth('user')->user()->id,
                'chef_id'=>$subscription->chef_id,
                'subscription_id'=>$subscription->id,
                'selected_delivery_time'=>Carbon::create($subscription->starts_at)->addDays( $meal->pivot->day_number)
                                        ->setTimeFromTimeString($subscription->meal_delivery_time)->toDateTimeString(),
                'notes'=>$request->notes,
                'status'=>'approved',
                'accepted_at'=>now(),
                'total_cost'=>$totalCost,
                'meals_cost'=>$mealsCost,
                'profit'=> $profits
            ]);
            $order->meals()->attach($meal->id, ['quantity' => 1,'notes'=>$request->notes]);
        });
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
