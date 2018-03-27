<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    //we're not going to use the default timestamps.
    public $timestamps = false;

    //We set the attributes that are mass assignable
    protected $fillable = [
        'date', 'time', 'contact_email'
    ];
}
