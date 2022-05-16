<?php

namespace App\Rules;
use Carbon\Carbon;

use Illuminate\Contracts\Validation\Rule;

class BeforeMidnight implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
       $midnight=new Carbon('midnight tomorrow');
       $time=new Carbon($value);
       return $time->lessThan($midnight);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ' :attribute should be before midnight.';
    }
}
