<?php

namespace App\Http\Controllers;

use App\Models\Chef;
use App\Models\Meal;
use App\Models\Order;
use App\Rules\InChefDeliveryRange;
use App\Rules\LessThenMaxMealsPerDay;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\MealsHelper;
use Illuminate\Support\Arr;

class OrderController extends Controller
{
    use MealsHelper;

    public function validateOrderParameters(Request $request){
        $validator = Validator::make($request->all(), [
            'chef_id'=>'required',
            'meals'=>'required',
            'meals.*.id'=>'required',
            'meals.*.quantity'=>'required',
            'selected_delivery_time'=>'required|date_format:Y-m-d H:i:s',
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $chef=Chef::findOrFail($request->chef_id);
         //check if selected time is in the range of the chef
        $validator = Validator::make($request->all(), [
            'selected_delivery_time'=>[new InChefDeliveryRange($chef)],
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        return $this->successResponse([]);
    }

    public function makeOrder(Request $request)
    {
        $validationResponse=$this->validateOrderParameters($request);
        if($validationResponse->getStatusCode()>=400)
            return $validationResponse;
        $chef=Chef::findOrFail($request->chef_id);
        $meals=Meal::findOrFail(array_values(Arr::pluck($request->meals,'id')));
        //check if chef has max meals per day
        $maxChefMeals=$chef->max_meals_per_day;
        $currentChefAssignedMeals=$this->getCountOfTodayAssingedTotalMeals($chef);
        $currentOrderMealsCount=0;//=$request->meals_count;
        foreach($request->meals as $meal){
            $currentOrderMealsCount+=$meal['quantity'];
        }
        if($currentChefAssignedMeals+$currentOrderMealsCount > $maxChefMeals)
            return $this->errorResponse('لا يمكن الطلب من هذا الطاهي لأنه وصل إلى الحد الأقصى من الطلبات',400);
        //check if quantities of meals is less than a max meal per day
        for($i=0;$i<$meals->count();$i++){
            $meal=$meals[$i];
            $mealQuantity=$request->meals[$i]['quantity'];
            $mealMaxPerDay=$meal->max_meals_per_day;
            $todayOrderedMealCount=$this->getCountOfTodayAssingedMeals($chef,$meal);
            if($todayOrderedMealCount +$mealQuantity >$mealMaxPerDay){
                $mealName=$meal->name;
                return $this->errorResponse('وصلت الوجبة '. $mealName .' إلى الحد الأقصى من العدد المسموح لطلبها اليوم',400);
            }
               
        }
        //check if chef or any meal is not available
        if(!$chef->is_available)
            return $this->errorResponse("الشيف غير متاح للطلب",400);
        foreach($meals as $meal){
                if(!$meal->is_available)
                    return $this->errorResponse("توجد وجبة غير متاحة للطلب",400);
        }
        //get meal cost
        $mealsCost=$request->meals_cost;
        //get profit
        $currentOrderMealsCount=0;//=$request->meals_count;
        foreach($request->meals as $meal){
            $currentOrderMealsCount+=$meal['quantity'];
        }
        $mealsProfit=$this->getMealProfit() *$currentOrderMealsCount ;
        //get delivery cost
        $deliveryFee=$this->getMealDeliveryFee($chef->id);
        //get total order cost
        $totalCost=$mealsCost+$mealsProfit+$deliveryFee;

        $order=Order::create([
            'user_id'=>auth('user')->user()->id,
            'chef_id'=>$request->chef_id,
            'selected_delivery_time'=>$request->selected_delivery_time,
            'notes'=>$request->notes,
            'total_cost'=>$totalCost,
            'meals_cost'=>$mealsCost,
            'profit'=>$mealsProfit
        ]);
        //attach order with meals 
        foreach($request->meals as $meal){
            $order->meals()->attach($meal['id'], ['quantity' => $meal['quantity'],'notes'=>$meal['notes']]);
        }
        return $this->successResponse(['message'=>"order created successfuly!"],201);
    }

    public function getCurrentDeliveryFee(Request $request){
        $chefId=$request->chef_id;
        return $this->successResponse(['delivery_fee'=>$this->getMealDeliveryFee($chefId)]);
    }

}
