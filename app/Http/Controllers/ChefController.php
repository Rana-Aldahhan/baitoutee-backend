<?php

namespace App\Http\Controllers;

use App\Models\Chef;
use App\Models\Category;
use App\Rules\BeforeMidnight;
use App\Rules\TimeAfter;
use App\Traits\MealsHelper;
use App\Traits\PictureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

// a controller to control the chef model
class ChefController extends Controller
{
    use MealsHelper,PictureHelper;
    public function show(Chef $chef){
        $chef->location_name=$chef->location->name;
        $chef->ratings=$this->getRate($chef)[0];
        $chef->rates_count=$this->getRate($chef)[1];
        $chef->orders_count=$chef->orders->where('subscription_id',null)->count();
        $chef->remaining_available_chef_meals_count=$chef->max_meals_per_day-$this->getCountOfTodayAssingedTotalMeals($chef);
        $chef->delivery_fee=$this->getMealDeliveryFee($chef->id);
        $chef=$chef->only(['name','delivery_starts_at','delivery_ends_at','is_available'
        ,'location_name','ratings','rates_count'
        ,'orders_count','max_meals_per_day','remaining_available_chef_meals_count']);
        return $this->successResponse($chef);
    }
    public function getChefCategories(Chef $chef)
    {

        $categorieIds=$chef->meals->pluck(['category_id'])->unique();
        $categories=Category::find($categorieIds);
        return $this->successResponse($categories);

    }
    public function getChefMealsOfCategory(Chef $chef, $categoryId){
        $meals=$chef->meals->where('category_id',$categoryId)->values();
        $meals->map(function ($meal)use($chef){
            $meal->remaining_available_meal_count=$meal->max_meals_per_day-$this->getCountOfTodayAssingedMeals($chef,$meal);
            if($meal->discount_percentage!=null)
                $meal->price_after_discount=( $meal->price -( ( $meal->price * $meal->discount_percentage) /100)) +$this->getMealProfit();
            $meal->setHidden(['chef','chef_id','created_at', 'updated_at', 'approved', 'max_meals_per_day', 'expected_preparation_time', 'ingredients', 'category', 'category_id']);
            return $meal->price = $meal->price + $this->getMealProfit() ;
           return $meal->only('name','is_available');
        });
        return $this->successResponse($meals);
    }

    /**
     * hide not needed information in the browse page
     * @param $chef
     * @return mixed
     */
    private function hideFromItem ($chef) {
        $chef->makeHidden('chef_join_request_id');
        $chef->makeHidden('phone_number');
        $chef->makeHidden('email');
        $chef->makeHidden('birth_date');
        $chef->makeHidden('gender');
        $chef->makeHidden('location_id');
        $chef->makeHidden('location');
        $chef->makeHidden('meals');
        $chef->makeHidden('delivery_starts_at');
        $chef->makeHidden('delivery_ends_at');
        $chef->makeHidden('max_meals_per_day');
        $chef->makeHidden('balance');
        $chef->makeHidden('certificate');
        $chef->makeHidden('approved_at');
        $chef->makeHidden('deleted_at');
        $chef->makeHidden('orders');
        return $chef;
    }

    /**
     * get the rating value and rating count of a chef depending on his meals
     * @param $chef
     * @return array
     */
    private function getRate ($chef){
        // following a method inspired by Bayesian probability
            // R refer to the "initial belief", 60th percentile (optimistic) or 40th percentile (pessimistic).
            // W is a fraction of number of rating, perhaps between C/20 and C/5 (depending on how noisy ratings are).
            // W = 0 is equivalent to using only the average of user ratings
            // W = infinity is equivalent to proclaiming that every item has a true rating of R
            // resource:
            // https://stackoverflow.com/questions/2495509/how-to-balance-number-of-ratings-versus-the-ratings-themselves
            $R = 2.5;  // 50% of 5 => 2.5
            $W = 10; // assuming 20000 numbers of rates for an item /2000
            // (R*W + sigma(n*rating)/(W+sigma(n))
            // depending on R and W if there is no rating => the item rate will be 25/10 = 2.5

        [$ratingCount, $ratingValue]  =  $chef->meals->reduceSpread(function ($ratingCount,$ratingValue,$meal) use ($R,$W)  {
            $ratingCount+=$meal->rates_count;
            $ratingValue += $meal->rates_count*$meal->rating;
            return [$ratingCount, $ratingValue] ;
            },0,0);
        $value = ($R*$W + ($ratingValue))/($W+$ratingCount);
        if($ratingCount == 0)
            $value = null;
        return [$value,$ratingCount];
}
    /**
     * a filter on the distance between the student and the chefs (get the nearest first)
     * for browsing page
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterNearest()
    {
        // get the location of the student
        $studentLocation = auth('user')->user()->location;
        // get the distances between chefs and the student
        // sort the chefs depending on the distances the lowest value first
        $sortedChefs = Chef::with('location')->get()->map(function ($chef) {
            $chef->chef_location = $chef->location->name;
            [$ratingValue,$ratingCount]  = $this->getRate($chef);
           $chef->chef_rate =$ratingValue;
           $chef->chef_rate_count = $ratingCount;
           return $this->hideFromItem($chef);
        })->sortBy(function ($chef) use ($studentLocation) {
            if ($studentLocation->id == 1)
                return $chef->location->distance_to_first_location;
            else if ($studentLocation->id == 2)
                return $chef->location->distance_to_second_location;
            else if ($studentLocation->id == 3)
                return $chef->location->distance_to_third_location;
        })->take(10)->values();
        return $this->successResponse($sortedChefs);
    }

    /**
     * a filter on the top-rated chefs to show in the browsing page
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterTopRated()
    {
        $sortedChefs = Chef::all()->map(function ($chef){
            $chef->chef_location = $chef->location->name;
            [$ratingValue,$ratingCount]  = $this->getRate($chef);
            $chef->chef_rate =$ratingValue;
            $chef->chef_rate_count = $ratingCount;
            return $this->hideFromItem($chef);
        })->sortByDesc(function ($chef){
            // get the meals of each chef
            [$ratingValue,$ratingCount]  = $this->getRate($chef);
            return $ratingValue ;
        })->values();
        return $this->successResponse($sortedChefs); // what to return ?
    }
    public function filterTopOrders()
    {
        //count orders of each chef
        // sort the chefs depending on the count
        $sortedChefs = Chef::all()->map(function ($chef){
            $chef->chef_location = $chef->location->name;
            [$ratingValue,$ratingCount]  = $this->getRate($chef);
            $chef->chef_rate =$ratingValue;
            $chef->chef_rate_count = $ratingCount;
            return $this->hideFromItem($chef);
        })->sortByDesc(fn($chef) => count($chef->orders->where('status','delivered')))->values();
        return $this->successResponse($sortedChefs);
    }

    public function filterNewestChefs()
    {
        $sortedChefs = Chef::all()->map(function ($chef){
            $chef->chef_location = $chef->location->name;
            [$ratingValue,$ratingCount]  = $this->getRate($chef);
            $chef->chef_rate =$ratingValue;
            $chef->chef_rate_count = $ratingCount;
            return $this->hideFromItem($chef);
        })->sortByDesc(fn($chef) => $chef->created_at)->values();
        return $this->successResponse($sortedChefs);
    }
    public function search(Request $request){
        $search = $request->search;
        $paginated_chefs = Chef::search($search)->paginate(10);

        $paginated_chefs->map(function ($chef){
            $mealsCount = $chef->meals->count();
            $chef->rating = $chef->meals->sum('rating')/ $mealsCount ;
            $chef->rates_count = $chef->meals->sum('rates_count');
            $this->hideFromItem($chef);
        });
        return $this->paginatedResponse($paginated_chefs);
    }

    // get the chef profile
    public function getProfile()
    {
        //return the picture + the name + the phone number + the location + the status(active or not)
        // with the balance the chef will get if he want to get paid for last
        $chefInfo = Chef::with('location')->where('chefs.id',auth('chef')->id())->first();
        $chefInfo->location_name = $chefInfo->location->name;
        $chefInfo->setHidden(['id','chef_join_request_id','created_at','updated_at','birth_date'
                    ,'gender','location_id','balance','approved_at','deleted_at'
                    ,'certificate','location']);
        return $this->successResponse($chefInfo);
    }


    //TODO: repeated between chef and delivery (need enhancement)
    public function getBalance(Request $request)
    {
        $chef= auth('chef')->user();
        $todayBalance = 0; $todayBalanceReceived =0;
        $thisWeekBalance =0; $thisWeekBalanceReceived =0;
        $thisMonthBalance =0;  $thisMonthBalanceReceived =0;
        $todayOrders =0; $thisWeekOrders=0; $thisMonthOrders =0;

        //map throw orders to get balance - what recieved - orders count for chef
        $chef->orders()->whereNotNull('prepared_at')->get()->map(function($order)
        use (&$todayBalance,&$thisWeekBalance,&$thisMonthBalance,
            &$todayBalanceReceived,&$thisWeekBalanceReceived,&$thisMonthBalanceReceived,
            &$todayOrders,&$thisWeekOrders,&$thisMonthOrders){
                $mealsCost =$order->meals_cost;
                $costRecivedCost=  $order->paid_to_chef;
            if($order->prepared_at?->isSameDay()){
                $todayBalance+= $mealsCost;
                $todayBalanceReceived+= $costRecivedCost?$mealsCost:0;
                $todayOrders+= 1;
            }
            //Note: that the week start from monday not sunday
            if($order->prepared_at?->isSameWeek()){
                $thisWeekBalance+= $mealsCost;
                $thisWeekBalanceReceived+= $costRecivedCost?$mealsCost:0;
                $thisWeekOrders+= 1;
            }
            if($order->prepared_at?->isCurrentMonth()){
                $thisMonthBalance+= $mealsCost;
                $thisMonthBalanceReceived+= $costRecivedCost?$mealsCost:0;
                $thisMonthOrders+= 1;
            }
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
                    ["balance"=>$chef->balance,
                     "today"=>$today,
                     "this_week"=>$thisWeek,
                     "this_month"=>$thisMonth]
                ));

    }

    public function editProfile (Request $request){
        // change the profile picture
        $validator = Validator::make($request->only('profile_picture'),
        ['profile_picture'=>'nullable']);
        if ($validator->fails()) { //case of input validation failure
           return $this->errorResponse($validator->errors()->first(), 422);
        }

        $imagePath =$this->storePublicFile($request,'profile_picture','profiles');
        $chef = auth('chef')->user();
        if ($imagePath != null) {
            if($chef->profile_picture !="")
                unlink(storage_path('app/public' . Str::after($chef->profile_picture, '/storage')));
            $chef->profile_picture = $imagePath;
            $chef->save();
        }

        return $this->successResponse([]);
    }

    // edit delivery_starts_at and/or delivery_ends_at
    public function editDeliverMealTime(Request $request)
    {
        $validator = Validator::make($request->only('delivery_starts_at','delivery_ends_at'),
        ['delivery_starts_at' =>['nullable','date_format:H:i:s'],
        'delivery_ends_at' =>['nullable','date_format:H:i:s',new TimeAfter($request['delivery_starts_at']), new BeforeMidnight]]);
        if ($validator->fails()) { //case of input validation failure
                return $this->errorResponse($validator->errors()->first(), 422);
        }
        $chef= auth('chef')->user();
        $is_updated = $chef->fill($validator->validated())->save();
        return $this->successResponse([]);
    }

    // edit max meals per day
    public function editMaxMealsPerDay(Request $request)
    {
        $validator = Validator::make($request->only('max_meals_per_day'),
        ['max_meals_per_day'=>'required|numeric']);
        if ($validator->fails()) { //case of input validation failure
                return $this->errorResponse($validator->errors()->first(), 422);
        }
        $chef= auth('chef')->user();
        $is_updated = $chef->fill($validator->validated())->save();
        return $this->successResponse([]);
    }
    public function changeAvailabilityStatus()
    {
        $chef=auth('chef')->user();
        $chef->is_available=!$chef->is_available;
        $chef->save();

        return $this->successResponse([]);
    }

}

