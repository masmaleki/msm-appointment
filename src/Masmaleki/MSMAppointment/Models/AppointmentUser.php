<?php

namespace Masmaleki\MSMAppointment\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointment_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','email','calendar_id','reference_table_id'];
}
