<?php

namespace App\Rules;

use DateTime;
use Illuminate\Contracts\Validation\Rule;

class NotPastDateTime implements Rule
{
    //properties that include the selected date and time.
    private $selectedDate;
    private $selectedTime;

    /**
     * Create a new rule instance.
     *
     * @param $selectedDate
     * @param $selectedTime
     */
    public function __construct($selectedDate, $selectedTime)
    {
        //We initialize the selected date and time properties value
        $this->selectedDate = DateTime::createFromFormat('Y-m-d', $selectedDate);
        $this->selectedTime = DateTime::createFromFormat('H:i:s', $selectedDate);

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
        //We will check that  both date and time attributes of the appointment aren't in the past inside this method
        //using the datetime passed in the constructor
        //The whole datetime must be greater than the current datetime
        $currentDatetime = new Datetime('now');
        //But if the selected date is the same than the current date or today
        $today = $currentDatetime->format('Y-m-d');
        if($today == $this->selectedDate){
            //we need to make sure that the selected time
            //is greater than the current time or now, but only in hours, because the appointments are of one hour
            //of duration
            $now = $currentDatetime->format('H');
            $this->selectedTime = $this->selectedTime->format('H');
            //return the logic operation result
            //true if the selected time is greater than now
            //false if the selected time is lower than now
            $result = $now < ($this->selectedTime);
            return $result;
        }
        //else, if the date is different from today, we need to make sure that
        //the whole selected datetime is greater than the current datetime
        else{
            $mergeDateTime = $this->selectedDate->format('Y-m-d').' '.$this->selectedTime->format('H:i:s');
            $result = $mergeDateTime > $currentDatetime;
            return $result;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given date and time refers to a past time. Please pick a valid combination of date and time.';
    }
}
