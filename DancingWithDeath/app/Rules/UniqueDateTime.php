<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class UniqueDateTime implements Rule
{
    //properties that include the selected date and time.
    private $selectedDate;
    private $selectedTime;
    private $tableName;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($selectedDate, $selectedTime, $tableName)
    {
        //We initialize the selected date and time properties value
        $this->selectedDate = $selectedDate;
        $this->selectedTime = $selectedTime;
        $this->tableName = $tableName;
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
        //
        $result = DB::table($this->tableName)->where('time', $this->selectedTime)->where('date', $this->selectedDate)->count();
        return $result === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "There's an appointment on that date and time.";
    }
}
