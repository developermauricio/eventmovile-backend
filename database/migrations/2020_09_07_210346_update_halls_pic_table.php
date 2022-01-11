<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHallsPicTable extends Migration
{
    
    public function up()
    {
        Schema::table('halls', function (Blueprint $table){
            $table->string('pic')->nullable();
        });
    }

    public function down()
    {
        Schema::table('halls', function(Blueprint $table) {
            $table->dropColumn('pic');
        });
    }
}
