<?php

namespace Masmaleki\MSMAppointment\Database\Seeds;

use Illuminate\Database\Seeder;
use Masmaleki\GoogleCalendar\Models\AppointmentUser;

class AppointmentUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'calendar_id' => 'YOUR_CALENDAR_ID',
                'email' => 'example@email.com',
                'name' => 'John Doe',
                'reference_table_id' => null
            ]
        ];

        foreach ($users as $key => $user) {
            AppointmentUser::create([$user]);
        }
    }
}