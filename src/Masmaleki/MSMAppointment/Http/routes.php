<?php

/*
|--------------------------------------------------------------------------
| MSMAppointment Application Register
|--------------------------------------------------------------------------
|
*/
use Masmaleki\MSMAppointment\Http\Controllers\AppointmentController;

$middleware = config('MSMAppointment.routes-middlewares');
//appointment
if (is_array($middleware)  && count($middleware)){
    Route::resource('appointments', 'AppointmentController')->middleware($middleware);
}else{
    Route::resource('appointments', 'AppointmentController');
}
