<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_events', function (Blueprint $table) {
            $table->id();
            $table->string('criteria_id');
            $table->string('name');
            $table->string('canonical_name');
            $table->string('parent_id');
            $table->string('country_code');
            $table->foreign('country_code')->references('alpha2Code')->on('country_events');
            $table->string('target_type');
            $table->string('status');
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
        Schema::dropIfExists('city_events');
    }
}
