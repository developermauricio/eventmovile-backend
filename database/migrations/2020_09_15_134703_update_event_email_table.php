<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventEmailTable extends Migration
{
    
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('message_email', 3000)->nullable();
        });
    }

    
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('message_email');
        });
    } 
}
