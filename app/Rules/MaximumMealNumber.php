<?php

namespace App\Rules;

namespace App\Rules;

use App\Models\Chef;
use App\Models\Meal;
use Illuminate\Contracts\Validation\Rule;

class MaximumMealNumber implements Rule
{


    /*
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /*
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */

    public function passes($attribute, $value)
    {

        $max_meals_per_day =  auth('chef')->user()->max_meals_per_day;
        $value = intval($value);
        return  $max_meals_per_day >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.selected_max_meals_per_day.MoreThanMaxMeals');
    }
}
