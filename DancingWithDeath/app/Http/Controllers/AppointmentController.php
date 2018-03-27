<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Rules\BusinessDay;
use App\Rules\UniqueDateTime;
use Validator;
use DateTime;
use App\Rules\NotPastDateTime;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Method that returns all the appointments in the database.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(){
        $appointments = Appointment::all();
        return $appointments;
    }


    /**
     *  Method that validates and store a new appointment
     * @param Request $request
     * @return array
     */
    public function store(Request $request){
        //We will manually create the validator
        //first we define the validation rules
        //An appointment must be in a business day, from 9:00 am to 6:00 pm.
        //And must be unique.
        //An appointment is only valid when it means present or future, not the past.
        //This last validation can be done on any of two attributes (date or time) new NotPastDateTime($request->date, $request->time).
        $validationRules = [
            'date' => ['required', 'date', new UniqueDateTime($request->date, $request->time,'appointments')],
            'time' => ['required', 'regex:/(09|1[0-8]):00:00/', new BusinessDay],
            'contact_email' => ['required', 'email', 'unique:appointments']
        ];

        //Then we define the messages that will be displayed if the validation fails
        $errorMessages = [
            'date.required' => "You must pick a date to make an appointment to dance with the Death. Please pick a date from monday to friday.",
            'time.required' => "You must pick the hour of your dance with the Death. Please pick the hour of your death.",
          'time.regex' => "We're sorry, the Death only dances in office hour, from 9:00 am to 6:00 pm. Please pick a valid hour.",
            'contact_email.email' => "You must enter a valid email.Please try again",
            'contact_email.unique' => "We're sorry, you have an appointment with the day. You're already dead.",
            'contact_email.required' => "We're sorry, the Death needs to know your email in order to dance with you. Please enter your email.",
        ];

        //we create the validator and validate the requests
        Validator::make($request->all(), $validationRules, $errorMessages)->validate();
        //If we pass the validations, we store the new appointment
        $newAppointment = new Appointment();
        $newAppointment->date = $request->date;
        $newAppointment->time = $request->time;
        $newAppointment->contact_email = $request->contact_email;
        $newAppointment->save();
        return ['message' => "You have successfully scheluded an appointment to Dance with the Death!."];
    }

    /**
     * @param $selectedDate
     * @return mixed
     */
    public function showHoursByDate($selectedDate){
        //we fetch all the existing appointments hours in the selected date
        //order by time asc
        $timeAppointments = Appointment::select('time')
            ->where('date', $selectedDate)
            ->orderBy('time', 'asc')->get();
        return $timeAppointments;
    }
}
