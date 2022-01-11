<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('halls', function (Blueprint $table){
            $table->bigInteger('hall_type_id')->nullable()->unsigned();
            $table->foreign('hall_type_id')->references('id')->on('hall_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('halls', function(Blueprint $table) {
            $table->dropColumn('hall_type_id');
        });
    }
}
