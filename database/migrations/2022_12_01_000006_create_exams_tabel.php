<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('class', 1);
            $table->string('name');
            $table->dateTime('starts')->nullable();
            $table->dateTime('due')->nullable();
            $table->tinyInteger('hours')->nullable();
            $table->tinyInteger('minutes')->nullable();
            $table->boolean('is_random')->default(0);
            $table->string('thumbnail')->default('exam/exam_image.png');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('exams');
    }
}
