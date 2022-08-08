<?php

namespace App\Http\Controllers;

use App\Events\OrderIsPrepared;
use App\Models\Chef;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Order;
use App\Rules\InChefDeliveryRange;
use App\Rules\TimeAfter;
use App\Traits\MealsHelper;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Services\FCMService;

class OrderController extends Controller
{
    use MealsHelper;

    public function validateOrderParameters(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chef_id' => 'required',
            'meals' => 'required',
            'meals.*.id' => 'required',
            'meals.*.quantity' => 'required',
            'selected_delivery_time' => 'required|date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        $chef = Chef::findOrFail($request->chef_id);
        //check if selected time is in the range of the chef
        $validator = Validator::make($request->all(), [
            'selected_delivery_time' => [new InChefDeliveryRange($chef),new TimeAfter(now())],
        ]);
        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        return $this->successResponse([]);
    }

    public function makeOrder(Request $request)
    {
        $validationResponse = $this->validateOrderParameters($request);
        if ($validationResponse->getStatusCode() >= 400) {
            return $validationResponse;
        }

        $chef = Chef::findOrFail($request->chef_id);
        $meals = Meal::findOrFail(array_values(Arr::pluck($request->meals, 'id')));
        //check if chef has max meals per day
        $maxChefMeals = $chef->max_meals_per_day;
        if(Carbon::create($request->selected_delivery_time)->isToday())
            $currentChefAssignedMeals = $this->getCountOfTodayAssingedTotalMeals($chef);
        else
            $currentChefAssignedMeals=$this->getCountOfTommorowAssingedTotalMeals($chef);
        $currentOrderMealsCount = 0; //=$request->meals_count;
        foreach ($request->meals as $meal) {
            $currentOrderMealsCount += $meal['quantity'];
        }
        if ($currentChefAssignedMeals + $currentOrderMealsCount > $maxChefMeals) {
            return $this->errorResponse('لا يمكن الطلب من هذا الطاهي لأنه وصل إلى الحد الأقصى من الطلبات', 400);
        }

        //check if quantities of meals is less than a max meal per day
        for ($i = 0; $i < $meals->count(); $i++) {
            $meal = $meals[$i];
            $mealQuantity = $request->meals[$i]['quantity'];
            $mealMaxPerDay = $meal->max_meals_per_day;
            if(Carbon::create($request->selected_delivery_time)->isToday())
                $todayOrderedMealCount = $this->getCountOfTodayAssingedMeals($chef, $meal);
            else
                $todayOrderedMealCount = $this->getCountOfTomorrowAssingedMeals($chef, $meal);
            if ($todayOrderedMealCount + $mealQuantity > $mealMaxPerDay) {
                $mealName = $meal->name;
                return $this->errorResponse('وصلت الوجبة ' . $mealName . ' إلى الحد الأقصى من العدد المسموح لطلبها اليوم', 400);
            }

        }
        //check if chef or any meal is not available
        if (!$chef->is_available) {
            return $this->errorResponse("الشيف غير متاح للطلب", 400);
        }

        foreach ($meals as $meal) {
            if($meal->chef->id != $request->chef_id)
                return $this->errorResponse("يجب أن تكون جميع الوجبات لطاهٍ واحد", 400);
            if (!$meal->is_available) {
                return $this->errorResponse("توجد وجبة غير متاحة للطلب", 400);
            }

        }
        //get meal cost (with profit)
        $mealsCost = $request->meals_cost;
        //get only profit
        $currentOrderMealsCount = 0; //=$request->meals_count;
        foreach ($request->meals as $meal) {
            $currentOrderMealsCount += $meal['quantity'];
        }
        $mealsProfit = $this->getMealProfit() * $currentOrderMealsCount;
        //get delivery cost
        $deliveryFee = $this->getMealDeliveryFee($chef->id);
        //get total order cost
        $totalCost = $mealsCost + $deliveryFee;

        $order = Order::create([
            'user_id' => auth('user')->user()->id,
            'chef_id' => $request->chef_id,
            'selected_delivery_time' => $request->selected_delivery_time,
            'notes' => $request->notes,
            'total_cost' => $totalCost,
            'meals_cost' => $mealsCost-$mealsProfit,
            'profit' => $mealsProfit,
            'payment_method'=>$request->payment_method
        ]);
        //attach order with meals
        foreach ($request->meals as $meal) {
            $order->meals()->attach($meal['id'], ['quantity' => $meal['quantity'], 'notes' => $meal['notes']]);
        }
        return $this->successResponse(['message' => "order created successfuly!"], 201);
    }

    public function getCurrentDeliveryFee(Request $request)
    {
        $chefId = $request->chef_id;
        return $this->successResponse(['delivery_fee' => $this->getMealDeliveryFee($chefId)]);
    }

    /**
     * show the meals with each one counts that will the chef will deliver in the next hours
     * until reach the end of time delivering at
     * @return jsonResponse// a collection that contains the houre and the collection of meals to deliver in that hour
     */
    public function indexForChefOrderedMeals()
    {
        $chefOrders = auth('chef')->user()->orders()
            ->whereDate('selected_delivery_time',Carbon::today())
            ->where('status', 'approved')
            ->orWhere('status', 'notAssigned')//TODO query might bring undesired recoreds
            ->whereDate('selected_delivery_time',Carbon::today())
            ->where('chef_id',auth('chef')->user()->id)
            ->get()
            ->groupby('selected_delivery_time')
            ->sortBy(function ($time) {
                $dt = DateTime::createFromFormat("Y-m-d H:i:s", $time[0]->selected_delivery_time);
                $hour = $dt->format('H');
                return $hour;
            })->map(function ($group) {
            return $group->map(function ($item) {
                return $item->meals;
            })->flatten()->map(function ($meal) {
                $meal->quantity = $meal->pivot->quantity;
                $meal->setHidden(['pivot', 'rates_count', 'rating', 'discount_percentage',
                    'is_available', 'price', 'created_at', 'updated_at', 'approved', 'max_meals_per_day',
                    'expected_preparation_time', 'ingredients', 'category', 'category_id', 'chef']);
                return $meal;
            })->reject(function ($meal) {
                return empty($meal);
            })->groupby('id')->map(function ($group) {
                return [
                    'id' => $group->first()['id'],
                    'chef_id' => $group->first()['chef_id'],
                    'image' => $group->first()['image'],
                    'name' => $group->first()['name'],
                    'quantity' => $group->sum('quantity'),
                ];
            })->values();
        });
        $orderMeals = $chefOrders->keys()->map(function ($key) use ($chefOrders) {
            return collect(['clock' => $key, 'meals' => $chefOrders->get($key)]);
        })->toArray();
        return $this->successResponse($orderMeals, 200);

    }

    /**
     * send the clock that you want the orders that should be delivered in
     * show the orders with the chef will deliver in the next hours
     * until reach the end of time delivering at
     * @return jsonResponse// a collection that contains the houre and the collection of orders to deliver in that hour
     */
    public function indexForChefOrders(Request $request)
    {
        $time = request('time');
        $request->merge(['time' => request('time')]);
        $validator = Validator::make($request->only('time'),
            ['time' => [
                'required',
                'date_format:H:i:s',
            ]]);

        if ($validator->fails()) { //case of input validation failure
            return $this->errorResponse($validator->errors()->first(), 422);
        }
        $chefOrders = auth('chef')->user()->orders()
            ->whereDate('selected_delivery_time',Carbon::today())
           ->where('selected_delivery_time', Carbon::create($time))
            ->where('status', 'approved')
            ->orWhere('status', 'notAssigned')//TODO query might bring undesired recoreds
            ->whereDate('selected_delivery_time',Carbon::today())
            ->where('selected_delivery_time', Carbon::create($time))
            ->where('chef_id',auth('chef')->user()->id)
            ->get()->flatten()
            ->map(function ($item) {
                $deliveryman = null;
                if ($item->delivery_id != null) {
                    $deliveryman = $item->delivery->deliveryman;
                }

                $notes = 'ملاحظات الطلب :' . $item->notes  . '\n ملاحظات الوجبات: \n';
                 $item->meals->map(function ($meal) use(&$notes) {
                    $notes=$notes.'الوجبة '.$meal->name.' : '.$meal->pivot->notes.'\n';
                    $meal->quantity = $meal->pivot->quantity;
                    $meal->setHidden(['category_id', 'max_meals_per_day', 'is_available',
                        'expected_preparation_time', 'ingredients', 'rates_count', 'rating',
                        'created_at', 'updated_at', 'approved',
                        'pivot', 'category', 'chef']);
                    $mealNote = $meal->name . ": " . $meal->pivot->notes;
                    return $notes .=$mealNote . ", ";
                });
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'selected_delivery_time' => $item->selected_delivery_time,
                    'subscription' => $item->subscription_id,
                    'notes' =>$item->$notes,
                    'meals' => $item->meals,

                    // 'deliveryman' => $deliveryman,
                ];

            });

        return $this->successResponse($chefOrders, 200);

    }
    /**
     * put the order done
     * @param Request $request
     * @param  Order $order
     */
    public function changeStatusToPrepared(Order $order)
    {
        if($order->status !='approved')
            return $this->errorResponse("لم يتم قبول هذه الوجبة من قبل الأدمن بعد",400);
        $updatedOrder = $order->update([
            'status' => "prepared",
            'prepared_at'=> now()
        ]);
        //braodcast event to deliverymen
        broadcast(new OrderIsPrepared($order))->toOthers();
        //send notification of order status change to user
        $user=$order->user;
        FCMService::sendPushNotification(
            $user->fcm_token,
            'تم تحضير طلبك',
            ' لقد تم إتمام تحضير طلبك رقم'.$order->id
        );
        //add  order meals cost to chef balance
        $chef=auth('chef')->user();
        $chef->balance+=$order->meals_cost;
        $chef->save();
        return $this->successResponse($updatedOrder);
    }

        // get the history order for chef
        public function getChefOrderHistory(Request $request)
        {
            // the history is the prepared orders (the order can't be changed to prepared if the admin did not accept the order)
            $orders = auth('chef')->user()->orders()
            ->where('status','prepared')->get();
            $orders->map(function($order){
                $order->setHidden(['user_id','chef_id','delivery_id','subscription_id',
                'total_cost','profit','accepted_at','selected_delivery_time','notes','paid_to_accountant',
                'created_at','updated_at']);
                if($order->subscription_id==null)
                    $order->type = "اشتراك";
                else $order->type ="وجبة فردية";
            });
            $sortedOrders = $orders->sortBy(function ($order){
                return $order->id;
            },SORT_REGULAR,true)->values()->paginate(10);
            return $this->paginatedResponse($sortedOrders);
        }
        // get the history order for deliverymen
        public function getDeliveriedOrderHistory(Request $request)
        {
            // the history is the delivered orders
            //dd(auth('deliveryman')->id());
            $orders = auth('deliveryman')->user()->deliveryOrders()->with('chef')
            ->with('user')->with('delivery')->get();

            $orderHistory = $orders->filter(function($order){
                // map on the orders to get the location of the chef
                //and the location of the user of each order
                $chefLocation = Location::find($order->chef->location_id);
                $userLocation = Location::find($order->user->location_id);
                 // get the meals quantities of each order
                $mealsQuantity = $order->meals()->get()->sum(function($meal){
                   return $meal->pivot->quantity;
                });
                //show desired information in each order
                $order->chef_location = $chefLocation->name;
                $order->distenation = $userLocation->name;
                $order->meals_quantity = $mealsQuantity;
                $order->delivered_at = $order->delivery->delivered_at->format('Y-m-d H:i:s');
                $order->delivery_cost = $order->delivery->delivaryman_cost_share;//$order->delivery_cost = $order->delivery->cost;
                $order->chef_name = $order->chef->name;
                // hide undesired information from each order
                $order->setHidden(['user_id','chef_id','delivery_id','subscription_id','status',
                'selected_delivery_time','notes','meals_cost','profit','accepted_at','prepared_at',
                'paid_to_chef','paid_to_accountant','created_at','updated_at','laravel_through_key',
                'chef','user','delivery']);
                if ($order->status == 'delivered')
                    return $order;
            });
            $sortedOrders = $orderHistory->sortBy(function ($order){
                return $order->id;
            },SORT_REGULAR,true)->values()->paginate(10);
            return $this->paginatedResponse($sortedOrders);
        }

    public function getNotes ()
    {
        $chef = auth('chef')->user();
        $meals = $chef->orders->map(function($order){
            if($order->status =='delivered'){
                // map on meals to see if the meal note is null or note
                // if the meal note is null then do not return the meal
                 $mealsWithRatingNotes = $order->meals->filter(function ($meal) use ($order){
                    // add desired keys
                    $meal->meal_id = $meal->pivot->meal_id;
                    $meal->prepared_at = $order->prepared_at;
                    $meal->order_id = $order->id;
                    $meal->meal_rate = $meal->pivot->meal_rate;
                    $meal->meal_rate_notes = $meal->pivot->meal_rate_notes;
                    // remove undesired keys
                    $meal->setHidden(['category_id','image','price','max_meals_per_day','is_available'
                    ,'expected_preparation_time','discount_percentage','ingredients','rating','rates_count'
                    ,'approved','chef','category','created_at','updated_at','chef_id','id','pivot']);
                    if($meal->pivot->meal_rate_notes != null){
                        return $meal;
                    }
                 });
                 return $mealsWithRatingNotes;
            }
        })->flatten()->filter();
        $sortedMeals = $meals->sortBy(function($col) {
            return $col->prepared_at;
        },SORT_REGULAR,true)->values()->all();
        return $this->successResponse($sortedMeals);
    }

}
