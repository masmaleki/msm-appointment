<?php

/*
|--------------------------------------------------------------------------
| MSMAppointment Application Register
|--------------------------------------------------------------------------
|
*/

$middleware = config('MSMAppointment.route-middlewares');
//appointment
if (is_array($middleware)  && count($middleware)){
    Route::resource('appointments', 'AppointmentController')->middleware(['verified', 'auth', 'can:panel-access','menuitem:666']);
}else{
    Route::resource('appointments', 'AppointmentController');
}
