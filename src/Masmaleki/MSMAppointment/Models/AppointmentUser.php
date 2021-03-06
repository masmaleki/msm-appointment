<?php

namespace Masmaleki\MSMAppointment\Models;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Spatie\GoogleCalendar\Event;

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

    public static function disableDates($users)
    {
        $disableDates = [];
        foreach ($users as $key => $user) {
            $events = Event::get(Carbon::now(),null,[],$user->calendar_id);
            $disableDates[$user->id] = [];
            foreach ($events as $key => $event) {
                
                $startDate = self::convertDateByTimezone($event->googleEvent->start->dateTime);
                $endDate   = self::convertDateByTimezone($event->googleEvent->end->dateTime);
                
                $tempDate = clone $startDate;
                $dif = $startDate->diff($endDate)->format('%H');

                $date = $tempDate->format('H') == 00 ? $startDate->addDays(1)->format('d/m/Y') : $startDate->format('d/m/Y');
                $times[$date] = [];

                array_push($times[$date], $tempDate->format('H'));

                for ($i=1; $i <= $dif; $i++) { 
                    $clone = clone $tempDate;
                    if ($clone->addHour() == $endDate && $clone->format('i') == '00') {
                        continue;
                    }
                    array_push($times[$date], $times[$date][$i] = $tempDate->addHour()->format('H'));
                }

                if (!isset($disableDates[$user->id][$date]) || !is_array($disableDates[$user->id][$date])) {
                    $disableDates[$user->id][$date] = [];
                }

                foreach ($times[$date] as $key => $value) {
                    if (in_array($value, $disableDates[$user->id][$date])) {
                        continue;
                    }
                    array_push($disableDates[$user->id][$date],$value);
                }
            }
        }
        return $disableDates;
    }
    public static function convertDateByTimezone($date) 
    { 
        $timezone = config('MSMAppointment.timezone');
        $date = new DateTime($date);
        $tz = $date->getTimezone();
        $tzName = $tz->getName();
        $date->setTimezone(new DateTimeZone($tz->getName()));
        return Carbon::parse($date->setTimezone(new DateTimeZone($timezone)));
    } 

}
