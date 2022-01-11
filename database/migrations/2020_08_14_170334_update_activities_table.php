<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActivitiesTable extends Migration
{
   
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedInteger('mode_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('activities', function(Blueprint $table) {
            $table->dropColumn('mode_id');
        });
    }
}
