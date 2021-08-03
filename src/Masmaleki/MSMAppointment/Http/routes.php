<?php

/*
|--------------------------------------------------------------------------
| MSMAppointment Application Register
|--------------------------------------------------------------------------
|
*/


$middleware = config('MSMAppointment.routes-middlewares');
//appointment
if (is_array($middleware)  && count($middleware)){
    Route::resource('appointments', AppointmentController::class)->middleware($middleware);
}else{
    Route::resource('appointments', 'AppointmentController');
}
Route::get('shaftaloo','AppointmentController@index');
