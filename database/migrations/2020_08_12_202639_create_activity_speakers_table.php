<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySpeakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_speakers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->bigInteger('speaker_id')->unsigned();
            $table->foreign('speaker_id')->references('id')->on('speakers');
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
        Schema::dropIfExists('activity_speakers');
    }
}
