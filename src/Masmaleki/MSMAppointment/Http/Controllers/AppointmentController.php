<?php

namespace Masmaleki\MSMAppointment\Http\Controllers;

use Masmaleki\MSMAppointment\Models\Appointment;
use Illuminate\Http\Request;
use Spatie\GoogleCalendar\Event;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Masmaleki\MSMAppointment\Models\AppointmentUser;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointments = Appointment::all();
        return view('msm-appointments::index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $edit = false;
        $users = AppointmentUser::all();
        $disableDates = AppointmentUser::disableDates($users);

        return view('msm-appointments::create_edit', compact('edit', 'users','disableDates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'start_date' => 'required|string',
            'start_time' => 'required|string',
            'user_id' => 'required|string',
            'client_description' => 'required|string',
            'client_name' => 'required|string',
            'client_email' => 'required|string',
            'client_phone' => 'required|string',
        ]);

        $timezoneName = config('MSMAppointment.timezone');

        $requestDate = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->format('Y-m-d') . 'T' . $request->get('start_time');
        $user = AppointmentUser::findOrFail($request->get('user_id'));

        $calendarId = $user->calendar_id;
        $startDate = Carbon::parse($requestDate, $timezoneName);
        $endDate = clone $startDate;
        $endDate = $endDate->addHour();
        $events = Event::get(Carbon::now(), null, [], $calendarId);

        if (!self::checkValidDate($events, $startDate, $endDate, $timezoneName)) {
            return redirect()->back()->withErrors('This time is not availble');
        }

        try {
            $event = Event::create([
                'name' => $request->get('name'),
                'startDateTime' => Carbon::parse($startDate->format(DateTime::RFC3339), $timezoneName),
                'endDateTime' => Carbon::parse($endDate->format(DateTime::RFC3339), $timezoneName),
                'description' => $request->get('client_description')
                    . ' | Name: ' . $request->get('client_name')
                    . ' | Email: ' . $request->get('client_email')
                    . ' | Phone: ' . $request->get('client_phone'),
                'visibility' => 'public'
            ], $calendarId);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Operation Failed');
        }

        $link = self::getLink($event, $calendarId);

        $uuid = self::getUUID();

        $appointment = Appointment::create([
            'name' => $request->get('name'),
            'start_date' => $event->googleEvent->start->dateTime,
            'appointment_user_id' => $user->id,
            'event_id' => $event->googleEvent->id,
            'link' => $link,
            'client_description' => $request->get('client_description'),
            'client_name' => $request->get('client_name'),
            'client_email' => $request->get('client_email'),
            'client_phone' => $request->get('client_phone'),
            'timezone' => $timezoneName,
            'status' => 'active',
            'uuid' => $uuid
        ]);

        $appointment->has_newsletter = $request->get('has_newsletter') == 'on' ? true : false;

        return redirect()->back()->with('appointment', $appointment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        $edit = false;
        $users = AppointmentUser::all();
        return view('msm-appointments::create_edit', compact('edit', 'appointment', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'start_date' => 'string',
            'user_id' => 'string',
            'client_description' => 'string',
            'client_name' => 'string',
            'client_email' => 'string',
            'client_phone' => 'string',
        ]);

        $timezoneName = $appointment->timezone;

        $user = AppointmentUser::findOrFail($request->get('user_id'));

        $calendarId = $user->calendar_id;
        $startDate = Carbon::parse($request->get('start_date'), $timezoneName);
        $endDate = clone $startDate;
        $endDate = $endDate->addHour();
        $events = Event::get(Carbon::now(), null, [], $calendarId);

        foreach ($events as $key => $event) {
            $eventStart = Carbon::parse($event->googleEvent->start->dateTime, $timezoneName);
            $eventEnd = Carbon::parse($event->googleEvent->end->dateTime, $timezoneName);
            if (
                $startDate->gte($eventStart) && $eventEnd->gte($startDate) ||
                $endDate->gte($eventStart) && $eventEnd->gte($endDate) ||
                $startDate->lte($eventStart) && $endDate->gte($eventEnd)
            ) {
                return redirect()->back()->withErrors('This time is not availble');
            }
        }
        try {
            $event = Event::find($appointment->event_id)->update([
                'name' => $request->get('name'),
                'startDateTime' => Carbon::parse($startDate->format(DateTime::RFC3339), $timezoneName),
                'endDateTime' => Carbon::parse($endDate->format(DateTime::RFC3339), $timezoneName),
                'description' => $request->get('client_description')
                    . ' | Name: ' . $request->get('client_name')
                    . ' | Email: ' . $request->get('client_email')
                    . ' | Phone: ' . $request->get('client_phone')
            ], $calendarId);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Operation Failed');
        }

        Appointment::where('id', $appointment->id)->update([
            'name' => $request->get('name'),
            'start_date' => $event->googleEvent->start->dateTime,
            'appointment_user_id' => $user->id,
            'event_id' => $event->googleEvent->id,
            'link' => $event->googleEvent->htmlLink,
            'client_description' => $request->get('client_description'),
            'client_name' => $request->get('client_name'),
            'client_email' => $request->get('client_email'),
            'client_phone' => $request->get('client_phone'),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        $user = AppointmentUser::findOrFail($appointment->appointment_user_id);
        Event::find($appointment->event_id, $user->calendar_id)->delete();
        $appointment->delete();

        return redirect()->back();
    }

    public function destroyWithUuid($uuid)
    {
        $appointment = Appointment::where('uuid', $uuid)->where('status', '!=', 'canceled')->first();
        if (!$appointment) {
            return redirect()->back()->withErrors('Appointment not found');
        }

        $user = AppointmentUser::findOrFail($appointment->appointment_user_id);
        Event::find($appointment->event_id, $user->calendar_id)->delete();
        $appointment->status = 'canceled';
        $appointment->save();

        return redirect()->back()->with(['message' => 'Appointment canceled successfully']);
    }

    public function updateWithUuid(Request $request, $uuid)
    {
        $this->validate($request, [
            'start_date' => 'string',
            'start_time' => 'string',
        ]);

        $appointment = Appointment::where('uuid', $uuid)->where('status', '!=', 'canceled')->first();

        if (!$appointment) {
            return redirect()->back()->withErrors('Appointment not found');
        }

        $timezoneName = $appointment->timezone;
        $user = AppointmentUser::findOrFail($appointment->appointment_user_id);
        $requestDate = Carbon::createFromFormat('d/m/Y', $request->get('start_date'))->format('Y-m-d') . 'T' . $request->get('start_time');

        $calendarId = $user->calendar_id;
        $startDate = Carbon::parse($requestDate, $timezoneName);
        $endDate = clone $startDate;
        $endDate = $endDate->addHour();
        $events = Event::get(Carbon::now(), null, [], $calendarId);

        if (!self::checkValidDate($events, $startDate, $endDate, $timezoneName)) {
            return redirect()->back()->withErrors('This time is not availble');
        }
        
        try {
            $event = Event::find($appointment->event_id,$calendarId)->update([
                'startDateTime' => Carbon::parse($startDate->format(DateTime::RFC3339), $timezoneName),
                'endDateTime' => Carbon::parse($endDate->format(DateTime::RFC3339), $timezoneName),
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Operation Failed');
        }

        $link = self::getLink($event, $calendarId);
        
        $appointment->start_date = $event->googleEvent->start->dateTime;
        $appointment->event_id = $event->googleEvent->id;
        $appointment->link = $link;
        $appointment->status = 'updated';
        $appointment->save();

        return redirect()->back()->with(['message' => 'Appointment updated successfully']);
    }


    public function findAppointmentByUuid(Request $request)
    {
        if (!$request->ajax()) {
            return false;
        }
        $this->validate($request, [
            'uuid' => 'required|string',
            // 'g-recaptcha-response' => 'required|captcha'
        ]);
        $appointment = Appointment::where('uuid', $request->get('uuid'))->where('status', '!=', 'canceled')->first();
        if (!$appointment) {
            $result['type'] = 'error';
            $result['title'] = __('notify.error');
            $result['msg'] = __('general.Item Not Found');
        } else {
            $result['type'] = 'success';
            $appointment->user_name = $appointment->user->name;
            $appointment->start_date = Carbon::parse($appointment->start_date)->toDateTimeString();
            $appointment->end_date = Carbon::parse($appointment->start_date)->addHour()->toDateTimeString();
            $result['appointment'] = $appointment;
        }
        return json_encode($result); 
    }


    public static function checkValidDate($events, $startDate, $endDate, $timezoneName)
    {
        foreach ($events as $key => $event) {
            $eventStart = Carbon::parse($event->googleEvent->start->dateTime, $timezoneName);
            $eventEnd = Carbon::parse($event->googleEvent->end->dateTime, $timezoneName);
            if (
                $startDate->gt($eventStart) && $startDate->lt($eventEnd) ||
                $endDate->gt($eventStart) && $endDate->lt($eventEnd) ||
                $startDate->lte($eventStart) && $endDate->gte($eventEnd)
            ) {
                return false;
            }
        }
        return true;
    }

    public static function getUUID($uuid = null)
    {
        if (is_null($uuid)) {
            return self::getUUID(Str::random(8));
        }

        if (!Appointment::where('uuid', $uuid)->exists()) {
            return $uuid;
        }

        return self::getUUID(Str::random(8));
    }

    public static function getLink($event, $calendarId)
    {
        $eventLink = $event->googleEvent->htmlLink;
        $url_components = parse_url($eventLink);
        parse_str($url_components['query'], $eventUrlParams);
        $eid = $eventUrlParams['eid'];

        $params = [
            'action' => 'TEMPLATE',
            'tmeid' => $eid,
            'tmsrc' => $calendarId
        ];

        $link = "https://calendar.google.com/event?" . http_build_query($params);
        return $link;
    }
}
