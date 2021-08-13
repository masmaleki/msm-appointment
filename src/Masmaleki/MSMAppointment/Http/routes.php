<?php

/*
|--------------------------------------------------------------------------
| MSMAppointment Application Register
|--------------------------------------------------------------------------
|
*/


$admin_middleware = config('MSMAppointment.admin_middlewares');
$user_middleware = config('MSMAppointment.user_middlewares');
if (is_array($admin_middleware)  && count($admin_middleware)){
    Route::get('appointments','AppointmentController@index')->middleware($admin_middleware);
    Route::get('appointments/{appointment}/edit','AppointmentController@edit')->middleware($admin_middleware);
    Route::put('appointments/{appointment}','AppointmentController@update')->middleware($admin_middleware);
    Route::delete('appointments/{appointment}/delete','AppointmentController@destroy')->middleware($admin_middleware);
}else{
    Route::get('appointments','AppointmentController@index');
    Route::get('appointments/{appointment}/edit','AppointmentController@edit');
    Route::put('appointments/{appointment}','AppointmentController@update');
    Route::delete('appointments/{appointment}/delete','AppointmentController@destroy');
}
if (is_array($user_middleware)  && count($user_middleware)){
    Route::get('appointments/create','AppointmentController@create')->middleware($user_middleware);
    Route::post('appointments','AppointmentController@store')->middleware($user_middleware);
    Route::delete('appointments-delete/uuid/{uuid}','AppointmentController@destroyWithUuid')->middleware($user_middleware);
    Route::put('appointments-update/uuid/{uuid}','AppointmentController@updateWithUuid')->middleware($user_middleware);
    Route::post('/appointment/find-by-uuid', 'AppointmentController@findAppointmentByUuid')->middleware($user_middleware);
} else {
    Route::get('appointments/create','AppointmentController@create');
    Route::post('appointments','AppointmentController@store');
    Route::delete('appointments-delete/uuid/{uuid}','AppointmentController@destroyWithUuid');
    Route::put('appointments-update /uuid/{uuid}','AppointmentController@updateWithUuid');
    Route::post('/appointment/find-by-uuid', 'AppointmentController@findAppointmentByUuid');
}
