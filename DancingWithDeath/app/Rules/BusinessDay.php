<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BusinessDay implements Rule
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
        //We parse the datetime value into a timestamp in order to validate that the date is a business day
        $timestampAux = strtotime($value);
        // 'w' is Numeric representation of the day of the week
        // in this representation, sunday = 0, monday = 1... saturday = 6;
        $weekDayNumber = date('w', $timestampAux);
        // Returns true if weekDayNumber between monday and friday
        // False if it's a weekend day
        $result = ($weekDayNumber > 0) && ($weekDayNumber < 6);
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "We're sorry, the Death only dances in business days, from monday to friday. Please pick a valid date.";
    }
}
