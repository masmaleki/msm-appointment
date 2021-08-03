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
            $table->integer('user_id');
            $table->string('user_email');
            $table->string('user_phone');
            $table->string('user_name');
            $table->string('subject');
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
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
        //
        Schema::drop('appointments');
    }

}
