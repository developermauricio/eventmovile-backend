<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            $table->string('name', 200);
            $table->string('sort_description', 400);
            $table->integer('unit_price')->default(0);
            $table->integer('duration_minutes')->default(60);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('pic');
            $table->string('code_streaming');
            $table->string('tags');
            $table->string('friendly_url');
            $table->string('location_coordinates');
            $table->string('address', 200);
            $table->bigInteger('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->integer('guests_limit')->default(0);
            $table->bigInteger('type_activity_id')->unsigned();
            $table->foreign('type_activity_id')->references('id')->on('type_activities');
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
        Schema::dropIfExists('activities');
    }
}
