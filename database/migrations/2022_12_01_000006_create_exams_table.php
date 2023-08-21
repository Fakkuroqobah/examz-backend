<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
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
            $table->string('class', 2);
            $table->string('name');
            $table->enum('status', ['inactive', 'launched', 'finished'])->default('inactive');
            $table->boolean('is_random')->default(0);
            $table->string('thumbnail')->default('exam/exam_image.png');
            $table->longText('description')->nullable();
            $table->integer('time')->comment('minutes');
            $table->boolean('is_rated')->default(0);
            $table->unsignedInteger('teacher_id');
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers');

            $table->unique('name');
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
