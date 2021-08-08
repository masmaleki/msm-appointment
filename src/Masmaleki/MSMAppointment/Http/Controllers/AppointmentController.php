<?php

namespace Masmaleki\MSMAppointment\Http\Controllers;

use Masmaleki\MSMAppointment\Models\Appointment;
use Illuminate\Http\Request;
use Spatie\GoogleCalendar\Event;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Masmaleki\MSMAppointment\Models\AppointmentUser;

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

        return view('msm-appointments::create_edit', compact('edit','users'));
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
            'start_date' => 'string',
            'user_id' => 'string',
            'client_description' => 'string',
            'client_name' => 'string',
            'client_email' => 'string',
            'client_phone' => 'string',
        ]);

        $user = AppointmentUser::findOrFail($request->get('user_id'));

        $calendarId = $user->calendar_id;
        $startDate = Carbon::parse($request->get('start_date'),'Asia/Tbilisi');
        $endDate = clone $startDate;
        $endDate = $endDate->addHour();
        $events = Event::get(Carbon::now(),null,[],$calendarId);
        
        foreach ($events as $key => $event) {
            $eventStart = Carbon::parse($event->googleEvent->start->dateTime,'Asia/Tbilisi');
            $eventEnd = Carbon::parse($event->googleEvent->end->dateTime,'Asia/Tbilisi');
            if ($startDate->gte($eventStart) && $eventEnd->gte($startDate) ||
                $endDate->gte($eventStart) && $eventEnd->gte($endDate) || 
                $startDate->lte($eventStart) && $endDate->gte($eventEnd)) {
                return redirect()->back()->withErrors('This time is not availble');
            }
        }

        try {
            $event = Event::create([  
                'name' => $request->get('name'),
                'startDateTime' => Carbon::parse($startDate->format(DateTime::RFC3339),'Asia/Tbilisi'),
                'endDateTime' => Carbon::parse($endDate->format(DateTime::RFC3339),'Asia/Tbilisi'),
                'description' => $request->get('client_description') 
                                . ' | Name: ' .$request->get('client_name')
                                . ' | Email: ' .$request->get('client_email')
                                . ' | Phone: ' .$request->get('client_phone')
            ],$calendarId);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Operation Failed');
        }

        $appointment = Appointment::create([
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
        return view('msm-appointments::create_edit', compact('edit','appointment','users'));
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

        $user = AppointmentUser::findOrFail($request->get('user_id'));

        $calendarId = $user->calendar_id;
        $startDate = Carbon::parse($request->get('start_date'),'Asia/Tbilisi');
        $endDate = clone $startDate;
        $endDate = $endDate->addHour();
        $events = Event::get(Carbon::now(),null,[],$calendarId);
        
        foreach ($events as $key => $event) {
            $eventStart = Carbon::parse($event->googleEvent->start->dateTime,'Asia/Tbilisi');
            $eventEnd = Carbon::parse($event->googleEvent->end->dateTime,'Asia/Tbilisi');
            if ($startDate->gte($eventStart) && $eventEnd->gte($startDate) ||
                $endDate->gte($eventStart) && $eventEnd->gte($endDate) || 
                $startDate->lte($eventStart) && $endDate->gte($eventEnd)) {
                return redirect()->back()->withErrors('This time is not availble');
            }
        }
        try {
            $event = Event::find($appointment->event_id)->update([  
                'name' => $request->get('name'),
                'startDateTime' => Carbon::parse($startDate->format(DateTime::RFC3339),'Asia/Tbilisi'),
                'endDateTime' => Carbon::parse($endDate->format(DateTime::RFC3339),'Asia/Tbilisi'),
                'description' => $request->get('client_description') 
                                . ' | Name: ' .$request->get('client_name')
                                . ' | Email: ' .$request->get('client_email')
                                . ' | Phone: ' .$request->get('client_phone')
            ],$calendarId);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Operation Failed');
        }
        
        Appointment::where('id',$appointment->id)->update([
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
}
