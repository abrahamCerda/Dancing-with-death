<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route that redirects to the store method of the AppointmentController class
Route::post('/appointments', 'AppointmentController@store')->name('appointment.store');

//Route that redirects to the show by date method of the AppointmentController class
//we validate that the parameter in the url has the format: dd/mm/yyyy or dd-mm-yyyy with a regExp
Route::get('/appointments/{date}', 'AppointmentController@showHoursByDate')
    //->where(['date' => '^([0-2][0-9]|3[0-1])(\/|-)(0[1-9]|1[0-2])\2(\d{4})$'])
    ->name('appointment.showHoursByDate');

//Route that redirects to the index method of the AppointmentController class
Route::get('/appointments', 'AppointmentController@index')-> name('appointment.index');