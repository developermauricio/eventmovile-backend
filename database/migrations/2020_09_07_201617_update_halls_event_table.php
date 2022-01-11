<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHallsEventTable extends Migration
{
    
    public function up()
    {
        Schema::table('halls', function (Blueprint $table){
            $table->bigInteger('event_id')->nullable()->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

   
    public function down()
    {
        Schema::table('halls', function(Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
}
