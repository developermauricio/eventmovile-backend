<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabeasDataTable extends Migration
{
    public function up()
    {
        Schema::create('habeas_data', function (Blueprint $table) {
            $table->id();
            $table->string('type', 15);
            $table->text('content');
            $table->string('position', 20)->default('down');
            $table->bigInteger('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('habeas_data');
    }
}
