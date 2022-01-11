<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventsThirdColorTable extends Migration
{
    
    public function up()
    {
        Schema::table('events', function (Blueprint $table){
            $table->string('third_color')->nullable();
        });
    }

    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('third_color');
        });
    }
}
