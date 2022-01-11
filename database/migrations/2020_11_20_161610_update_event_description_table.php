<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventDescriptionTable extends Migration
{
    
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    
    public function down()
    {
        //
    }
}
