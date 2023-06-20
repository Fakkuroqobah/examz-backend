<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswerEssayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer_essay', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question_id');
            $table->longText('default_answer');

            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');

            $table->unique('question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answer_essay');
    }
}
