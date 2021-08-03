<?php

/*
 * This file is part of Laravel appointment management.
 * Mohammad Sadegh Maleki <masmaleki@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [


    /*
    |--------------------------------------------------------------------------
    | Appointment App route middleware Setting
    |--------------------------------------------------------------------------
    |
    | Here you can add each middleware you need or you have in your system to he
    | middle ware arrays then use on the routes.php
    |
    */

    'routes-middlewares' => [
        'verified',
        'auth',
        'can:panel-access',
        'menuitem:666',
    ],

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        /*
         * Authenticate using a service account.
         */
        'service_account' => [
            /*
             * Path to the json file containing the credentials.
             */
            'credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),
        ],
    ],

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

];