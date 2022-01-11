<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_activities', function (Blueprint $table) {
            $table->id();
            $table->string('question', 300);
            $table->string('answer', 300)->nullable();
            $table->bigInteger('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('question_activities');
    }
}
