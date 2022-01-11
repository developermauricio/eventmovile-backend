<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probes', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->string('question', 200);
            $table->json('options')->nullabel();
            $table->integer('position')->nullable();
            $table->bigInteger('type_question_id')->unsigned();
            $table->foreign('type_question_id')->references('id')->on('type_questions');
            $table->boolean('required')->default(false);
            
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
        Schema::dropIfExists('probes');
    }
}
