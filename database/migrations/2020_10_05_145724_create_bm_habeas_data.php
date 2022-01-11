<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBmHabeasData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bm_habeas_data', function (Blueprint $table) {
            $table->id();
            $table->string('type', 15);
            $table->string('content');
            $table->string('position', 20)->default('down');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business_markets');
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
        Schema::dropIfExists('bm_habeas_data');
    }
}
