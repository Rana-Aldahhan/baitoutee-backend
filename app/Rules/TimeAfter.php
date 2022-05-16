<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class TimeAfter implements Rule
{
    public $firstTime;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($firstTime)
    {
        $this->firstTime=$firstTime;
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
        $first=new Carbon ($this->firstTime);
        $second=new Carbon ($value);
        return $second->greaterThan($first);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ' :attribute يجب أن يكون أكبر من '.$this->firstTime;
    }
}
