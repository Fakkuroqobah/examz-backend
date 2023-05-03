<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('student_id');
            $table->boolean('status')->default(0);
            $table->boolean('block')->default(0);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            $table->foreign('room_id')->references('room_id')->on('schedules')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_schedule');
    }
}
