<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('appointment_user_id');
            $table->string('start_date');
            $table->text('link');
            $table->string('event_id');
            $table->string('client_email');
            $table->string('client_name');
            $table->string('client_phone');
            $table->text('client_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('appointments');
    }

}
