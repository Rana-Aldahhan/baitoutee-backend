<?php

namespace App\Http\Controllers;

use App\Models\Chef;
use App\Models\Category;
use App\Traits\MealsHelper;
use Illuminate\Http\Request;

// a controller to control the chef model
class ChefController extends Controller
{
    use MealsHelper;
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
}

