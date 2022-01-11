<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProbesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('probes', function (Blueprint $table) {
            $table->id();

            $table->boolean('required_probe')->default(false);
            $table->string('name', 200);
            $table->string('status')->default('ready');;
            $table->bigInteger('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');

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
        Schema::dropIfExists('probes');
    }
}
