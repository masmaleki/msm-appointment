<?php

namespace Masmaleki\MSMAppointment\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'start_date',
        'appointment_user_id',
        'link',
        'event_id',
        'client_email',
        'client_name',
        'client_phone',
        'client_description',
    ];
}
