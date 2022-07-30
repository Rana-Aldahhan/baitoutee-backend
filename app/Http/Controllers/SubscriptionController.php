<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Order;
use App\Models\Chef;
use App\Models\Meal;
use App\Rules\AcceptableMealsCost;
use App\Rules\TimeAfter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\MealsHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Rules\InChefDeliveryRange;
use Illuminate\Support\Arr;
use App\Services\FCMService;

class SubscriptionController extends Controller
{
    use MealsHelper;

    public function validateSubscriptionParameters(Request $request,$fieldRequired)
    {
        $validator = Validator::make($request->all(), [
            'name' => $fieldRequired,
            'days_number' => $fieldRequired.'|numeric|min:1',
            'meals' => $fieldRequired.'|array|size:'.$request->days_number,
            'starts_at' => [$fieldRequired,'date_format:Y-m-d H:i:s', new TimeAfter(Carbon::tomorrow()->toDateString())],
            'meal_delivery_time' => [$fieldRequired,'date_format:H:i:s',new InChefDeliveryRange(auth('chef')->user())],
            'max_subscribers' => $fieldRequired.'|numeric',
            'meals_cost'=> [$fieldRequired,'numeric',new AcceptableMealsCost($request->meals)]
        ],[
            'days_number.min' => 'يجب أن يكون :attribute واحد على الأقل ',
            'meals.size' => 'يجب أن يكون عدد الوجبات مساو لعدد أيام الاشتراك '
        ],['starts_at'=>'تاريخ بداية الاشتراك',
           'meal_delivery_time' => 'وقت توصيل وجبات الاشتراك',
            'days_number'=> ' عدد أيام الاشتراك']);
        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        return $validator;
    }
    // a function to let the chef store a subscription
    public function store(Request $request)
    {
        $validationResponse = $this->validateSubscriptionParameters($request,'required');
        if ($validationResponse instanceof JsonResponse) {
            return $validationResponse;
        }
        $meals = Meal::findOrFail($request->meals);
        $mealsCost = 0;
        foreach ($meals as $meal){
            if($meal->approved != 1)
                return $this->errorResponse('يوجد وجبة أو أكثر  تم اختيارها لم يتم الموافقة عليها بعد', 400);
            if($meal->chef_id != auth('chef')->id())
                return $this->errorResponse('يوجد وجبة أو أكثر  تم اختيارها غير موجودة في قائمة الوجبات', 400);
            $mealsCost+= $meal->price;
        }
        // create the subscription
        $subscription = Subscription::create([
            'chef_id' => auth('chef')->id(),
            'name' => $request->name,
            'days_number' => $request->days_number,
            'meal_delivery_time' => $request->meal_delivery_time,
            'max_subscribers' => $request->max_subscribers,
            'is_available'=> 0,
            'starts_at'=> $request->starts_at,
            'meals_cost' => $request->meals_cost,
        ]);
        //attach subscription with meals
        $day =1;
        foreach ($request->meals as $meal) {
            $subscription->meals()->attach($meal,['day_number' => $day]);
            $day++;
        }
        return $this->successResponse(['message' => "subscription created successfuly!"], 201);
    }
    // show subscription for chef
    public function indexForChef()
    {
        $subscriptions = auth('chef')->user()->subscriptions->
        filter(function($subscription) {
            $subscription->setHidden(['chef_id','created_at','updated_at','chef']);
            $numOfSubscribers =$this->getSubscribersCount($subscription);
            $subscription->current_subscribers = $numOfSubscribers;
            $subscription->meals =  $subscription->meals;
            if(Carbon::now()->isBefore(Carbon::create($subscription->starts_at))||
                (Carbon::now()->isBetween(Carbon::create($subscription->starts_at),
                 Carbon::create($subscription->starts_at)->addDays(intval($subscription->days_number)+1)))) {
                    $subscription->meals->map(function($meal){
                    $meal->day_number = $meal->pivot->day_number;
                    $meal->setHidden(['chef','pivot','category','ingredients','max_meals_per_day',
                'is_available','expected_preparation_time','discount_percentage','rates_count',
                'rating','created_at','updated_at','category_id','approved','chef_id']);
                 });

              return [
                'id' => $subscription->id,
                'name' => $subscription->name,
                'days_number' => $subscription->days_number,
                'meal_delivery_time' => $subscription->meal_delivery_time,
                'is_available' =>$subscription->is_available,
                'starts_at' => $subscription->starts_at,
                'max_subscribers'=> $subscription->max_subscribers,
                'meals_cost'=>$subscription->meals_cost,
                'current_subscribers' =>  $subscription->current_subscribers,
                'meals'=> $subscription->meals,
            ];
            }
          })->flatten();
        return $this->successResponse($subscriptions->toArray());
    }

    /**
     * edit the subscription by a chef
     *   notes:
     *  the request must have days_number and meals the rest of request parameters are not required
     *
     * @param Request $request
     * @param  Subscription $subscription
     * @return JsonResponse
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validationResponse = $this->validateSubscriptionParameters($request,'filled');
        if ($validationResponse instanceof JsonResponse) {

            return $validationResponse;
        }
        $numOfSubscribers =$this->getSubscribersCount($subscription);
        if($numOfSubscribers>0){
            $msg = 'لا يمكن تعديل الاشتراك لأنه قد تم الاشتراك يه من قبل '.$numOfSubscribers.' مشترك';
            return $this->errorResponse($msg,400);
        }
        $updatedData = Arr::except($validationResponse->validated(),['meals']);
        $is_updated = $subscription->fill($updatedData)->save();

        // delete the the meals that was before update
        $subscription->meals()->sync($request->meals[0]);
        $day =1;
        //attach subscription with meals
        foreach ($request->meals as $meal) {
            $subscription->meals()->syncWithPivotValues($meal,['day_number' => $day],false);
            $day++;
        }
        return $this->successResponse($is_updated);
    }

    /**
     * delete the subscription
     */
    public function destroy(Subscription $subscription)
    {
        // on delete cascade so there is no need to the detach (delete the pivote table columns)
        $numOfSubscribers =$this->getSubscribersCount($subscription);
        if($numOfSubscribers>0){
            $msg = 'لا يمكن حذف الاشتراك لأنه قد تم الاشتراك يه من قبل '.$numOfSubscribers.' مشترك';
            return $this->errorResponse($msg,400);
        }
        $success = $subscription->delete();
        if ($success) {
            return $this->successResponse([], 200);
        } else {
            return $this->errorResponse("لم يتمكن من حذف الوجبة", 404);
        }
    }

    /**
     * edit the availability of a subscription
     *
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function editAvailability(Subscription $subscription)
    {
        $newAvailability = !$subscription->is_available;
        $chefSubscriptions = auth('chef')->user()->subscriptions();
        $msg ="";
        $updatedMeal = $subscription->update([
            'is_available' => $newAvailability,
        ]);
        if($newAvailability == false) {
            $subscribersCount = $subscription->users()
            ->where('subscription_id', '=', $subscription->id )
            ->count();
            if($subscribersCount>0){
                $msg = ' تم إلغاء فعالية الاشتراك ولكنه متاح لمن اشترك قبل الإلغاء وهم '.$subscribersCount.' مشترك ';
            }
        }
        else if($newAvailability == true) {
            $numOfSubscribers =  $chefSubscriptions
            ->get()->sum(function ($subscription) {
                if($subscription->is_available)
                    return $subscription->max_subscribers;
            });
            $numOfAvailableSubscriptions = $chefSubscriptions->where('is_available',1)->count();
            $msg = 'أصبح لديك '.$numOfAvailableSubscriptions.' اشتراك متاح للمستخدمين حيث يمكن أن يشترك '.$numOfSubscribers.' مستخدم';
        }
        if ($updatedMeal) {
            return $this->successResponse($msg);
        } else {
            return $this->errorResponse("لم يتمكن من تفعيل الاشتراك", 404);
        }
    }

    public function getTopTenAvaialble()
    {
        $subscriptions=Subscription::where('is_available',true)
        ->where('starts_at','>',Carbon::today())
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
        ->where('starts_at','>',Carbon::today())
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
                $chef=Chef::find($subscription->chef->id);
                $subscription->chef->location=$chef->location->name;
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
        $subscriptions=$chef->subscriptions()
        ->where('is_available',true)
        ->where('starts_at','>',now())
        ->orderBy('starts_at')->get();
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
        //send notification of new subscriber to chef
        FCMService::sendPushNotification(
            $subscription->chef->fcm_token,
            'مشترك جديد',
            ' لقد تم إضافة مشترك جديد إلى الاشتراك '.$subscription->name
        ); 

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
    /**
     * search for a subscription and filter the results on days number if
     * the user send days in the request
     */
    public function searchAndFilter (Request $request){
        // get the word want to search for and what to filter on
        $search = $request->search;
        //return the records that fit with the search
        $paginated_subscribtions = Subscription::with(['meals'])
        ->whereHas('meals', function($query) use ($search) {
          $query->where('name','like', '%' . $search . '%');
          })->orWhere('name','like', '%' . $search . '%')
          ->where('is_available',1)
          ->get()->filter(function($subscription) use ($request) {
              $subscription->total_cost = $this->getTotalSubscriptionPrice($subscription);
              $numOfSubscribers =$this->getSubscribersCount($subscription);
              $subscription->current_subscribers = $numOfSubscribers;
              $subscription->setHidden(['chef_id','created_at','updated_at','meals_cost','meal_delivery_time',
              'max_subscribers','current_subscribers','is_available']);
              //FIXED: not recount the repeated meals
              $mealsCount = $subscription->meals->unique()->where('rating','!=',null)->count();
              $subscription->rating = $subscription->meals->unique()->sum('rating')/$mealsCount;
              $subscription->rates_count =  $subscription->meals->unique()->sum('rates_count');
              $mealsName = $subscription->meals->transform(function($meal){
                  $meal->price =  $meal->price + $this->getMealProfit(); // price without delivering
                  return $meal->name;
              });
              $subscription->meals = $mealsName;
              // return the subscription if it didn't start yet and it has the same days number filtered on
              if(Carbon::now()->isBefore(Carbon::create($subscription->starts_at))
                  && (($request->days!=null)?$subscription->days_number==$request->days:true)
                  && $subscription->is_available == true){
                      return $subscription;
              }
          })->values()->paginate(10);

          return $this->paginatedResponse($paginated_subscribtions);
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
    private function getSubscribersCount ($subscription){
        return $subscription->users()
        ->where('subscription_id', '=', $subscription->id )
        ->count();
    }


}
