<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventUsersTypesTable extends Migration
{
    
    public function up()
    {
        Schema::table('event_users', function (Blueprint $table){
            $table->bigInteger('event_type_id')->nullable()->unsigned();
            $table->foreign('event_type_id')->references('id')->on('event_types');
        });
    }

 
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('event_type_id');
        });
    }
}
