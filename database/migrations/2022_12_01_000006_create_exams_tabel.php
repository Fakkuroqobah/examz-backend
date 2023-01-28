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
            $table->string('class', 2);
            $table->string('name');
            $table->enum('status', ['inactive', 'launched', 'finished'])->default('inactive');
            $table->string('token', 5)->nullable();
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
