<?php

namespace App\Http\Controllers;

use App\Models\Chef;
use Illuminate\Http\Request;
use App\Traits\DistanceCalculator;
// a controller to control the chef model
class ChefController extends Controller
{

    private function hideFromItem ($item) {
        $item->makeHidden('chef_join_request_id');
        $item->makeHidden('phone_number');
        $item->makeHidden('email');
        $item->makeHidden('birth_date');
        $item->makeHidden('gender');
        $item->makeHidden('location_id');
        $item->makeHidden('delivery_starts_at');
        $item->makeHidden('delivery_ends_at');
        $item->makeHidden('max_meals_per_day');
        $item->makeHidden('balance');
        $item->makeHidden('certificate');
        $item->makeHidden('approved_at');
        $item->makeHidden('deleted_at');
        return $item;
    }
    // a filter on the distance between the student and the chefs (get the nearest first)
    // for browsing page
    public function filterNearest()
    {

        // get the location of the student
        $studentLocation = auth('user')->user()->location;
        // get the distances between chefs and the student
        // sort the chefs depending on the distances the lowest value first
        //$chef->deleted_at !=null?$isblocked=true;

        $sortedChefs = Chef::all()->map(fn($item) => $this->hideFromItem($item))->sortBy(function ($chef) use ($studentLocation) {
            $chef->meals->rates_count;
            $chef->meals->rating;
            if ($studentLocation->id == 1)
                return $chef->location->distance_to_first_location;
            else if ($studentLocation->id == 2)
                return $chef->location->distance_to_second_location;
            else if ($studentLocation->id == 3)
                return $chef->location->distance_to_third_location;
        })->take(10)->values();
        return $this->successResponse($sortedChefs);
    }


        // a filter on the top-rated chefs to show in the browsing page
        public function filterTopRated()
        {
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

            $sortedChefs = Chef::all()->map(fn($item) => $this->hideFromItem($item))->sortByDesc(function ($chef) use ($R,$W){
                // get the meals of each chef
                [$ratingCount, $ratingValue]  =  $chef->meals->reduceSpread(function ($ratingCount,$ratingValue,$meal) use ($R,$W)  {
                    $ratingCount+=$meal->rates_count;
                    $ratingValue += $meal->rates_count*$meal->rating;
                    return [$ratingCount, $ratingValue] ;
                },0,0);
                return ($R*$W + ($ratingValue))/($W+$ratingCount);
            })->values();
            return $this->successResponse($sortedChefs); // what to return ?
        }
        public function filterTopOrders()
        {
            //count orders of each chef
            // sort the chefs depending on the count
            $sortedChefs = Chef::all()->map(fn($item) => $this->hideFromItem($item))->sortByDesc(fn($chef) =>
            count($chef->orders->where('status','delivered')))->values();
            return $this->successResponse($sortedChefs);
        }

        public function filterNewestChefs()
        {
            $sortedChefs = Chef::all()->sortByDesc(fn($chef) => $chef->created_at)->values();
            return $this->successResponse($sortedChefs);
        }
    }

