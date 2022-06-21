<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class InChefDeliveryRange implements Rule
{
    public $chef;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($chef)
    {
        $this->chef=$chef;
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
        $delivery_starts_at=new Carbon($this->chef->delivery_starts_at);
        $delivery_ends_at=new Carbon($this->chef->delivery_ends_at);
        $selected_dalivery_time=new Carbon ($value);
        return ($selected_dalivery_time->greaterThanOrEqualTo($delivery_starts_at)
               && $selected_dalivery_time->lessThanOrEqualTo($delivery_ends_at))
              //also accept tomorrow orders
            || ($selected_dalivery_time->greaterThanOrEqualTo($delivery_starts_at->addDay())
                && $selected_dalivery_time->lessThanOrEqualTo($delivery_ends_at->addDay()))
               ;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.selected_delivery_time.InChefDeliveryRange');
    }
}
