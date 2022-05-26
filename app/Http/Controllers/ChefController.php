<?php

namespace App\Http\Controllers;

use App\Models\Chef;
use Illuminate\Http\Request;
use App\Traits\DistanceCalculator;
// a controller to control the chef model
class ChefController extends Controller
{
    use DistanceCalculator;

    // a filter on the distance between the student and the chefs (get the nearest first)
    // for browsing page
    public function filterNearest()
    {
        // get the location of the student
        $studentLocation = auth('user')->user()->location;
        // get the distances between chefs and the student
        // sort the chefs depending on the distances the lowest value first
        $sortedChefs = Chef::all()->sortBy(function ($chef) use ($studentLocation) {
            if ($studentLocation->id == 1)
                return $chef->location->distance_to_first_location;
            else if ($studentLocation->id == 2)
                return $chef->location->distance_to_second_location;
            else if ($studentLocation->id == 3)
                return $chef->location->distance_to_third_location;
        })->values();
        return $this->successResponse($sortedChefs);
    }


        // a filter on the top-rated chefs to show in the browsing page
        public
        function filterTopRated()
        {
            // following a method inspired by Bayesian probability
            // R refer to the "initial belief", 60th percentile (optimistic) or 40th percentile (pessimistic).
            // W is a fraction of number of rating, perhaps between C/20 and C/5 (depending on how noisy ratings are).
            // W = 0 is equivalent to using only the average of user ratings
            // W = infinity is equivalent to proclaiming that every item has a true rating of R

            $R = 2.5;  // 50% of 5 => 2.5
            $W = 10; // assuming 20000 numbers of rates for an item /2000
            // (R*W + sigma(n*rating)/(W+sigma(n))
            // depending on R and W if there is no rating => the item rate will be 25/10 = 2.5


            // get the meals of each chef
            // for each chef count the rate by multiply the rating with the rates count

        }

        public
        function filterTopOrders()
        {
        }

        public
        function filterNewestChefs()
        {
        }
    }

