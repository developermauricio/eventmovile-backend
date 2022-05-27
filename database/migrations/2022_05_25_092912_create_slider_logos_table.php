<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSliderLogosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider_logos', function (Blueprint $table) {
            $table->id();
            $table->string('title_logo')->nullable();   
            $table->string('name_logo')->nullable();  
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events'); 
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
        Schema::dropIfExists('slider_logos');
    }
}
