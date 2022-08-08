<?php

namespace App\Rules;

use App\Models\Meal;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class AcceptableMealsCost implements Rule
{

    public $mealsIDs;
    public $mealsCost;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $mealsIDs)
    {
        $this->mealsIDs = $mealsIDs;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $mealsCost = 0;

        foreach($this->mealsIDs as $mealID){
           $meal = Meal::find($mealID);
           $mealsCost+= $meal?->price;
        }
        $this->mealsCost =$mealsCost ;
        return (intval($value)<= $mealsCost && intval($value)>0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $msg = 'سعر الاشتراك يجب أن يكون أقل أو يساوي '.$this->mealsCost.' مجموع أسعار الوجبات الذي يحتويها';
        return $msg ;
    }
}
